<?php

namespace App\Accessors\Order;

use App\Accessors\Dictionnaries;
use App\Accessors\OrderAccessor;
use App\Enum\OrderClientType;
use App\Models\EventContact;
use App\Models\EventManager\Accommodation as Hotel;
use App\Models\EventManager\EventGroup;
use Illuminate\Support\Collection;
use Throwable;

class RoomingListAccessor extends AccommodationAccessor
{

    private ?Collection $bookingData = null;
    private ?Collection $parsedData = null;

    private int $count = 0;


    public function __construct(public ?Hotel $hotel)
    {
        $this->setEventAccommodation($this->hotel);
        $this->setEventFromAccommodation();
        $this->parseData();
    }

    public function getData(): Collection
    {
        if (is_null($this->parsedData)) {
            $this->parsedData = $this->makeDataSet();
        }

        return $this->parsedData;
    }

    protected function makeDataSet(): Collection
    {
        if ($this->bookingData->isEmpty()) {
            return collect();
        }

        $contactClientIds = $this->bookingData->filter(fn($item) => isset($item[0]['client_type']) && $item[0]['client_type'] === OrderClientType::CONTACT->value)->map(fn($item, $key) => $key)->values()->all();
        $groupClientIds   = $this->bookingData->filter(fn($item) => isset($item[0]['client_type']) && $item[0]['client_type'] === OrderClientType::GROUP->value)->map(fn($item, $key) => $key)->values()->all();
        $eventContactIds  = EventContact::where(fn($q) => $q->where('event_id', $this->hotel->event_id)->whereIn('user_id', $contactClientIds))->pluck('id', 'user_id')->toArray();
        $eventGroupsIds   = EventGroup::where(fn($q) => $q->where('event_id', $this->hotel->event_id)->whereIn('group_id', $groupClientIds))->pluck('id', 'group_id')->toArray();

        $this->bookingData = $this->bookingData->sortKeys();
        $data              = [];

        foreach ($this->bookingData as $client_id => $orders) {
            $this->count += $orders->count();

            foreach ($orders as $order) {
                $invoiceable = $order->invoiceable;

                $orderAccessor = (new OrderAccessor($order));
                $account       = $orderAccessor->account();

                if ($orderAccessor->hasAmendedAnotherOrder()) {
                    $invoiceable = (new OrderAccessor($orderAccessor->getAmendedOrder()))->invoiceable();
                }

                $accompanying = $order->accompanying->filter(fn($item) => $item['room_id'] == $order->accommodation->first()?->room_id)->first();

                try {
                    $dataline = [
                        'beneficiary_id'     => $client_id,
                        'event_contact_id'   => $account['type'] != OrderClientType::GROUP->value ? $eventContactIds[$client_id] : null,
                        'event_group_id'     => $account['type'] == OrderClientType::GROUP->value ? $eventGroupsIds[$client_id] : null,
                        'order_id'           => $order->id,
                        'order_client_type'  => $account['type'],
                        'last_name'          => $account['last_name'],
                        'first_name'         => $account['first_name'],
                        'email'              => $account['email'],
                        'company'            => $account['company'],
                        'country'            => $account['country_name'],
                        'roomnotes'          => $order->roomnotes->filter(fn($item) => $item->room_id == $order->accommodation->first()?->room_id)->first()?->note,
                        'invoiceable'        => $invoiceable?->account?->names(),
                        'order_total'        => $order->total_net + $order->total_vat,
                        'order_pec'          => $order->total_pec,
                        'payments_total'     => $order->payments->sum('amount'),
                        'order_status'       => $order->status,
                        'pec'                => $order->total_pec,
                        'pax'                => 1 + ($accompanying ? $accompanying->total : 0),
                        'accompanying'       => ($accompanying ? nl2br($accompanying->names) : ''),
                        'accommodation_cost' => $order->accommodation->sum('total_net') + $order->accommodation->sum('total_vat'),
                        'participation_type' => Dictionnaries::participationTypesListable((int)$order->participation_type_id, 'Participant'),

                    ];
                } catch (Throwable $e) {
                    report($e);
                    continue;
                }

                foreach ($order->accommodation as $cart) {
                    $cancelled = $order->cancelled_at ? $order->cancelled_at->format('d/m/Y') : $cart->cancelled_at?->format('d/m/Y');
                    $cartData  = [
                        'accommodation_cart_id' => $cart->id,
                        'date'                  => $cart->date->format('d/m/Y'),
                        'room_category_id'      => $cart->room_group_id,
                        'room_id'               => $cart->room_id,
                        'room_category_label'   => $this->roomGroups()[$cart->room_group_id] ?? 'NC',
                        'room_label'            => $this->rooms()[$cart->room_id] ?? 'NC',
                        'quantity'              => $cart->quantity,
                        'cancelled_at'          => $cancelled,
                        'cancellation_type'     => $cancelled ? ($order->cancelled_at ? 'order' : 'cart') : '',
                        'paid_price'            => $cart->total_vat + $cart->total_net,

                    ];
                    $data[]    = array_merge($dataline, $cartData);
                }
            }
        }

        return collect($data)
            ->sortBy(['date', 'last_name'])
            ->groupBy(['order_id', 'beneficiary_id', 'room_id']);
    }

    private function parseData(): void
    {
        if (is_null($this->bookingData)) {
            $this->bookingData = $this->bookings()->groupBy('client_id');
        }
    }

    public function getCounter(): int
    {
        return $this->count;
    }


}
