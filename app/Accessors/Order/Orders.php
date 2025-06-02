<?php

namespace App\Accessors\Order;

use App\Accessors\OrderAccessor;
use App\Enum\OrderClientType;
use App\Enum\OrderMarker;
use App\Enum\OrderType;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\Order;
use App\Models\Order\Invoiceable;
use Illuminate\Support\Collection;


class Orders
{
    public static function orderContainsGrantDeposit(Order $order): bool
    {
        return $order->grantDeposit->isNotEmpty();
    }

    public static function getUserLastOrder(int $userId): ?Order
    {
        return Order::where('client_id', $userId)->latest('created_at')->first();
    }

    public static function ownerHasCancelled(Order $order): bool
    {
        $orderAccessor = (new OrderAccessor($order));

        if ($orderAccessor->isRegular()) {
            $ec = EventContact::where('event_id', $order->event_id)
                ->where('user_id', $order->client_id)
                ->first();
            if ($ec?->order_cancellation) {
                return true;
            }
        } elseif ($orderAccessor->isGroup()) {
            // todo ...
        }

        return false;
    }

    public static function orderContainsServiceOfFamily(Order $order, int $serviceFamilyId): bool
    {
        return $order
            ->services()
            ->join('event_sellable_service', 'event_sellable_service.id', '=', 'order_cart_service.service_id')
            ->where('event_sellable_service.service_group', $serviceFamilyId)
            ->exists();
    }

    public static function getEventDashboardOrders(Event $event, EventContact $eventContact): ?Collection
    {
        return Order::whereIn(
            'id',
            Order::query()
                ->select('id as order_id')
                ->where([
                    'event_id'  => $event->id,
                    'client_id' => $eventContact->user_id,
                    'marker'    => OrderMarker::NORMAL->value,
                    'type' => OrderType::ORDER->value,
                ])
                ->whereIn('client_type', [OrderClientType::CONTACT->value, OrderClientType::ORATOR->value])
                ->with(['invoices', 'refunds'])
                ->union(
                    Invoiceable::query()
                        ->select('order_id')
                        ->where([
                            'account_id'   => $eventContact->user_id,
                            'account_type' => 'contact',
                        ])->join(
                            'orders as o',
                            fn($join)
                                => $join
                                ->on('o.id', '=', 'order_invoiceable.order_id')
                                ->where('o.event_id', $event->id)
                                ->where('o.marker', OrderMarker::NORMAL->value),
                        ),
                )->pluck('order_id'),
        )
            ->with(['group', 'account', 'invoiceable', 'payments'])->get() ?? collect();
    }

}
