<?php

namespace App\Http\Controllers\Order;

use App\Accessors\EventContactAccessor;
use App\Accessors\OrderAccessor;
use App\Accessors\OrderRequestAccessor;
use App\Actions\Order\OrderAccommodationActions;
use App\Enum\OrderAmendedType;
use App\Enum\OrderClientType;
use App\Models\Event;
use App\Models\Order;
use App\Models\Order\Cart\AccommodationCart;
use App\Traits\OrderPecTrait;
use App\Traits\OrderTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MetaFramework\Accessors\VatAccessor;
use Throwable;

class AmendedAccommodationCartController
{
    use OrderPecTrait;
    use OrderTrait;

    public function amend(Event $event, Order $order, AccommodationCart $cart)
    {

        if ($order->event_id != $event->id) {
            abort(404, "Cette commande n'est pas associée à cet évènement.");
        }


        if ($cart->order_id != $order->id) {
            abort(404, "Cet élément de réservation n'est pas associé à cette commande.");
        }

        $orderAccessor = new OrderAccessor($order);

        return view('orders.amend_accommodation')->with([
            'edit' => false,
            'event' => $event,
            'order' => $order,
            'amended_order' => $cart->order,
            'amendable_cart' => $cart,
            'amended_cart' => null,
            'orderAccessor' => $orderAccessor,
            'attributedTo' => $orderAccessor->attributedTo(),
            'samePayer' => $orderAccessor->isSamePayer(),
            'invoiceable' => $orderAccessor->invoiceableAddress(),
            'invoiced' => $orderAccessor->isInvoiced(),
            'event_contact' => EventContactAccessor::getData($event->id, $order->client_id),
            'route' => route('panel.manager.event.orders.accommodation.store-amended', [
                'event' => $event->id,
                'order' => $order->id,
                'cart' => $cart->id
            ])
        ]);

    }

    public function edit(Order $order)
    {
        $orderAccessor = new OrderAccessor($order);

        return view('orders.amend_accommodation')->with([
            'edit' => true,
            'event' => $order->event,
            'order' => $order,
            'amended_order' => $order->amendedOrder,
            'amended_cart' => $order->amendedAccommodation(),
            'orderAccessor' => $orderAccessor,
            'attributedTo' => $orderAccessor->attributedTo(),
            'samePayer' => $orderAccessor->isSamePayer(),
            'invoiceable' => $orderAccessor->invoiceableAddress(),
            'invoiced' => $orderAccessor->isInvoiced(),
            'event_contact' => EventContactAccessor::getData($order->event_id, $order->client_id),
            'route' => route('panel.manager.event.orders.update', [
                'event' => $order->event->id,
                'order' => $order->id
            ]),
            'as_orator' => $order->client_type == OrderClientType::ORATOR->value,
        ]);

    }

    public function store(Event $event, Order $order, AccommodationCart $cart)
    {
        //de(request()->all());
        $this->evaluatePossiblePec($event);
        $this->pecComputeAmounts();

        DB::beginTransaction();

        try {

            try {
                $created_at = Carbon::createFromFormat('d/m/Y', request('order.date'));
            } catch (Throwable) {
                $created_at = now();
            }

            $total_net = OrderRequestAccessor::getTotalNetFromRequest() - $this->amountPecNet;
            $total_vat = OrderRequestAccessor::getTotalVatFromRequest() - $this->amountPecVat;
            $vat_id = request('shopping_cart_accommodation.vat.0');

            $amended_total = ($total_net + $total_vat) - ($cart->total_net + $cart->total_vat);


            $this->order = Order::create([
                'event_id' => $event->id,
                'uuid' => request('order_uuid'),
                'created_by' => auth()->id(),
                'client_id' => $order->client_id,
                'client_type' => $order->client_type,
                'total_net' => VatAccessor::netPriceFromVatPrice($amended_total, $vat_id),
                'total_vat' => VatAccessor::vatForPrice($amended_total, $vat_id),
                'total_pec' => $this->amountPecNet + $this->amountPecVat,
                'created_at' => $created_at,
                'external_invoice' => $order->external_invoice,
                'po' => request('order.po'),
                'note' => request('order.note'),
                'terms' => request('order.terms'),
                'amended_order_id' => $cart->order->id,
                'amend_type' => OrderAmendedType::CART->value,
            ]);

            $cart->order->amended_by_order_id = $this->order->id;
            $cart->order->amend_type = OrderAmendedType::CART->value;
            $cart->order->save();


            $this->setOrderPecState();
            if ($order->invoiceable) {
                $this->order->invoiceable()->save($order->invoiceable);
            }
            $this->processAccompanying();
            $this->processRoomnotes();

            $this->order->setAmendedAccommodationCart($cart);
            OrderAccommodationActions::attachAccommodationToOrder($this->order);

            $this->deleteTempStock();
            $this->pecResponse();

            DB::commit();

            $this->responseSuccess("La commande a été créée");
            $this->redirect_to = route('panel.manager.event.orders.edit', [$event, $this->order]);

        } catch (Throwable $e) {
            $this->responseException($e);

            DB::rollBack();
        } finally {

            return $this->sendResponse();
        }

    }

}
