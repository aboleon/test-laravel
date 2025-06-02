<?php

namespace App\Actions\Order;

use App\Accessors\GroupAccessor;
use App\Enum\OrderCartType;
use App\Enum\OrderOrigin;
use App\Models\Event;
use App\Models\Order;
use App\Models\Order\Cart\ServiceAttribution;
use Illuminate\Validation\Rule;
use MetaFramework\Accessors\VatAccessor;
use MetaFramework\Traits\Ajax;
use Throwable;

class OrderServiceActions
{
    use Ajax;


    public function __construct()
    {
        $this->ajaxMode();
    }

    public function removeServiceAttribution(): array
    {
        try {
            $model = ServiceAttribution::findOrfail((int)request('id'));
            $this->responseElement('to_restore', $model->quantity);
            $model->delete();
            $this->responseSuccess("L'attribution a été supprimée");
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->fetchResponse();
    }

    public function removeFrontServiceAttribution(): array
    {
        request()->validate([
            'serviceId'      => [
                'required',
                'integer',
                Rule::exists('order_attributions', 'shoppable_id')
                    ->where('shoppable_type', 'service'),
            ],
            'eventContactId' => ['required', 'integer', 'exists:order_attributions,event_contact_id'],
            'qty'            => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->deleteFrontAttributionRecords(
                request('serviceId'),
                request('eventContactId'),
                request('qty'),
            );

            $this->responseSuccess(__('mfw.record.deleted'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->fetchResponse();
    }

    public function updateServiceAttributions(): array
    {
        $this->enableAjaxMode();

        $groupAccessor = new GroupAccessor(request('group_id'));
        $groupAccessor->setEvent(request('event_id'));

        $event        = Event::find(request('event_id'));
        $origin       = request('origin');
        $order_id     = (int)request('order_id');
        $isFrontOrder = $origin == OrderOrigin::FRONT->value;

        // Ça vient du front, il faut se préparer à recouper
        $ordered = [];
        if ($isFrontOrder) {
            $ordered = $groupAccessor->stockServiceQuery();
        }


        try {
            $data = [];
            for ($i = 0; $i < count(request('service_id')); ++$i) {
                $quantity         = (int)request('quantity.'.$i);
                $shoppable_id     = request('service_id.'.$i);
                $event_contact_id = request('event_contact_id.'.$i);

                if ($isFrontOrder) {
                    $attributed = $event->serviceAttributions
                        ->filter(fn($a) => $a->shoppable_id == $shoppable_id)
                        ->groupBy('order_id')
                        ->mapWithKeys(fn($group, $orderId)
                            => [
                            $orderId => $group->sum('quantity'),
                        ])->toArray();

                    $ordersForAttribution = $this->allocateOrders($ordered, $attributed, $quantity);

                    if (count($ordersForAttribution) < 2) {
                        $order_id              = key($ordersForAttribution);
                        $record                = ServiceAttribution::create(
                            $this->pushAttrubutionData(
                                $order_id,
                                $shoppable_id,
                                $quantity,
                                $event_contact_id,
                            ),
                        );
                        $data[$shoppable_id][] = ['member_id' => $event_contact_id, 'id' => $record->id, 'qty' => $quantity];
                    } else {
                        foreach ($ordersForAttribution as $order_id => $quantity) {
                            $record                = ServiceAttribution::create(
                                $this->pushAttrubutionData(
                                    $order_id,
                                    $shoppable_id,
                                    $quantity,
                                    $event_contact_id,
                                ),
                            );
                            $data[$shoppable_id][] = ['member_id' => $event_contact_id, 'id' => $record->id, 'qty' => $quantity];
                        }
                    }
                } else {
                    $record                = ServiceAttribution::create([
                        'order_id'         => $order_id,
                        'shoppable_id'     => $shoppable_id,
                        'shoppable_type'   => OrderCartType::SERVICE->value,
                        'quantity'         => $quantity,
                        'event_contact_id' => $event_contact_id,
                        'assigned_by'      => auth()->id(),
                    ]);
                    $data[$shoppable_id][] = ['member_id' => $event_contact_id, 'id' => $record->id, 'qty' => $quantity];
                }
            }

            $this->responseElement('stored', $data);
            $this->responseElement('callback', 'postServiceCreateAttributions');
            $this->responseElement('type', 'service');
            $this->responseElement('affected_date', now()->format('m/d/Y'));

            $this->responseSuccess("Les attributions ont été mises à jour.");
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->fetchResponse();
    }

    public static function attachServiceToOrder(Order $order): void
    {
        if (request()->filled('shopping_cart_service')) {
            if ( ! $order->wasRecentlyCreated) {
                $order->services()->delete();
            }

            $data = request('shopping_cart_service');

            $services = [];

            for ($i = 0; $i < count($data['id']); ++$i) {
                $qty     = $data['quantity'][$i];
                $vatId   = $data['vat_id'][$i];
                $cartPec = 0;


                $isPecAuthorized = $order->pecAuthorized && $data['pec_enabled'][$i];

                $cartNet   = VatAccessor::netPriceFromVatPrice($data['unit_price'][$i] * $qty, $vatId);
                $cartVat   = VatAccessor::vatForPrice($data['unit_price'][$i] * $qty, $vatId);
                $cartTotal = $cartVat + $cartNet;

                // Etablir quelle est la quantité PEC autorisée
                if ($isPecAuthorized && $data['pec_max'][$i] > 0) {
                    $noPecQty = 0;
                    if ($qty > $data['pec_max'][$i]) {
                        if ($data['pec_booked'][$i] < $data['pec_max'][$i]) {
                            $noPecQty = $data['quantity'][$i] - ($data['pec_max'][$i] - $data['pec_booked'][$i]);
                        }
                    } elseif ($qty == $data['pec_max'][$i]) {
                        $noPecQty = $data['pec_booked'][$i] > 0 ? $qty - $data['pec_booked'][$i] : 0;
                    } else {
                        $noPecQty = $qty - $data['pec_max'][$i];
                    }

                    $pecQuantity = $qty - $noPecQty;
                    $cartPec     = $data['unit_price'][$i] * $pecQuantity;
                }

                $services[] = new Order\Cart\ServiceCart([
                    'service_id' => $data['id'][$i],
                    'quantity'   => $data['quantity'][$i],
                    'unit_price' => $data['unit_price'][$i],
                    'total_net'  => $isPecAuthorized & $cartPec > 0 ? VatAccessor::netPriceFromVatPrice($cartTotal - $cartPec, $vatId) : $cartNet,
                    'total_vat'  => $isPecAuthorized & $cartPec > 0 ? VatAccessor::vatForPrice($cartTotal - $cartPec, $vatId) : $cartVat,
                    'total_pec'  => $cartPec,
                    'vat_id'     => $vatId,
                ]);
            }
            $order->services()->saveMany($services);
        }
    }

    /**
     * Allocates orders based on available ordered elements, previously attributed quantities, and the needed quantity.
     *
     * @param  array  $orderedElements     Array of stdClass objects with properties: order_id, date, room_id, quantity.
     * @param  array  $attributedElements  Associative array where keys are order_ids and values are already attributed quantities.
     * @param  int    $neededQuantity      The quantity needed for the next attribution.
     *
     * @return array Associative array where keys are order_ids and values are the assigned quantities to fulfill the need.
     */
    private function allocateOrders(array $orderedElements, array $attributedElements, int $neededQuantity): array
    {
        $result        = [];
        $remainingNeed = $neededQuantity;

        foreach ($orderedElements as $element) {
            $availableQuantity = $element->quantity - ($attributedElements[$element->order_id] ?? 0);

            if ($availableQuantity > 0) {
                $assignedQuantity = min($availableQuantity, $remainingNeed);

                $result[$element->order_id] = $assignedQuantity;

                $remainingNeed -= $assignedQuantity;

                if ($remainingNeed <= 0) {
                    break;
                }
            }
        }

        return $result;
    }

    private function pushAttrubutionData(int $order_id, int $shoppable_id, int $quantity, int $event_contact_id): array
    {
        return [
            'order_id'         => $order_id,
            'shoppable_id'     => $shoppable_id,
            'shoppable_type'   => OrderCartType::SERVICE->value,
            'quantity'         => $quantity,
            'event_contact_id' => $event_contact_id,
            'assigned_by'      => auth()->id(),
        ];
    }

    function deleteFrontAttributionRecords(
        int $shoppableId,
        int $eventContactId,
        int $qty,
    ): void {
        $records = Order\Attribution::query()
            ->where('shoppable_type', OrderCartType::SERVICE->value)
            ->where('shoppable_id', $shoppableId)
            ->where('event_contact_id', $eventContactId)
            ->orderBy('order_id')
            ->get(['id', 'order_id', 'quantity']);


        $remainingQty = $qty;

        foreach ($records as $record) {
            if ($record->quantity >= $remainingQty) {
                $record->quantity === $remainingQty
                    ? Order\Attribution::query()->where('id', $record->id)->delete()
                    : Order\Attribution::query()
                    ->where('id', $record->id)
                    ->update(['quantity' => $record->quantity - $remainingQty]);
                break;
            } else {
                Order\Attribution::query()->where('id', $record->id)->delete();
                $remainingQty -= $record->quantity;
            }
        }
    }

}
