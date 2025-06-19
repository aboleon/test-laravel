<?php

namespace App\Accessors\Front\Sellable;

use App\Accessors\EventContactAccessor;
use App\Enum\EventDepositStatus;
use App\Models\EventContact;
use App\Models\Order\Cart\ServiceCart;
use Illuminate\Support\Collection;

class Service
{
    /**
     * Get service items for an EventContact
     */
    public static function getServiceItems(EventContactAccessor|EventContact $accessor): Collection
    {
        if ($accessor instanceof EventContact) {
            $eventContactAccessor = new EventContactAccessor();
            $eventContactAccessor->setEventContact($accessor);
            $eventContact = $accessor;
        } else {
            $eventContactAccessor = $accessor;
            $eventContact = $eventContactAccessor->getEventContact();
        }

        // Use the accessor's method to get orders with services
        $ordersWithServices = $eventContactAccessor->getOrdersWithServices();

        // Extract service carts from orders
        $cartServices = $ordersWithServices->map(function ($order) {
            return $order->services;
        })->flatten();

        // Fetch attributed services with serviceCart relationship
        $attributedServices = $eventContactAccessor->serviceAttributions()->load('order');

        // Get sellable deposits for the event contact
        $sellableDeposits = $eventContact->sellableDeposits()
            ->whereIn('status', EventDepositStatus::paid())
            ->get()
            ->keyBy('shoppable_id');

        // Map original service carts
        $mappedServices = $cartServices->map(function (ServiceCart $cartService) use ($eventContact, $sellableDeposits) {
            return self::formatServiceItem($cartService, $eventContact, 'order', $sellableDeposits);
        });

        // Map attributed services
        $mappedAttributions = $attributedServices->map(function ($attribution) use ($eventContact, $sellableDeposits) {
            $service  = $attribution->shoppable;
            $quantity = $attribution->quantity ?? 1;

            // Get the unit price from the related service cart
            $unitPrice  = $attribution->cart?->unit_price ?: 0;
            $totalPrice = $unitPrice * $quantity;

            // Check for deposit
            $deposit = $sellableDeposits->get($service->id);
            $depositAmount = $service->deposit?->amount ?? 0;
            $depositPaid = $deposit ? 1 : 0;

            // Get service group information
            $serviceGroup = $service->group;
            $serviceGroupId = $serviceGroup?->id ?? null;
            $servicePosition = null;

            if ($serviceGroup && $attribution->order->event) {
                $eventService = $attribution->order->event->services()
                    ->where('event_service.service_id', $serviceGroupId)
                    ->first();
                $servicePosition = $eventService?->fo_family_position ?? null;
            }

            return [
                'title'        => $service->title ?? __('front/services.dashboard_no_title'),
                'text'         => implode('<br>', [
                    __('front/services.dashboard_quantity').": ".$quantity,
                    __('front/services.dashboard_price').": ".__('front/ui.free_of_charge'),
                    __('front/services.dashboard_unit_price').": ".$unitPrice." €",
                    __('front/services.dashboard_total').": ".$totalPrice." €",
                ]),
                'extra_badges' => [],
                'badge'        => [
                    'class' => 'text-bg-warning rounded-pill',
                    'text'  => __('front/ui.attribution_paid_by', ['payer' => $attribution->order->client()->names()]),
                ],
                'source'       => 'attribution',
                'paid_by'      => $attribution->order->client()->names(),
                'order_id'     => $attribution->order->id,
                'event_id'     => $attribution->order->event_id,
                'total_pec'    => $attribution->cart?->total_pec ?: 0,
                'unit_price'   => $unitPrice,
                'total_price'  => $totalPrice,
                'deposit_amount' => $depositAmount,
                'deposit_paid'   => $depositPaid,
                'service_group'  => $serviceGroupId,
                'service_position' => $servicePosition,
            ];
        });

        // Combine original and attributed services
        return $mappedServices->merge($mappedAttributions)->sortBy('service_position')->values();
    }

    // Helper method updated to include deposit information
    private static function formatServiceItem(ServiceCart $cartService, EventContact $eventContact, string $source, Collection $sellableDeposits): array
    {
        $service     = $cartService->service;
        $badge       = [];
        $extraBadges = [];

        // Check for deposit
        $deposit = $sellableDeposits->get($service->id);
        $depositAmount = $service->deposit?->amount ?? 0;
        $depositPaid = $deposit ? 1 : 0;

        // Get service group information
        $serviceGroup = $service->group;
        $serviceGroupId = $serviceGroup?->id ?? null;
        $servicePosition = null;

        if ($serviceGroup && $cartService->order->event) {
            $eventService = $cartService->order->event->services()
                ->where('event_service.service_id', $serviceGroupId)
                ->first();
            $servicePosition = $eventService?->fo_family_position ?? null;
        }

        if ($cartService->order->client_id !== $eventContact->user_id) {
            $payerName = $cartService->order->client()->names();
            $badge     = [
                'badge' => [
                    'class' => 'text-bg-purple rounded-pill',
                    'text'  => __('front/ui.paid_by', ['payer' => $payerName]),
                ],
            ];
        }

        if ($cartService->cancelled_at) {
            $extraBadges[] = [
                'class' => 'text-bg-danger rounded-pill',
                'text'  => __('front/order.cancelled'),
            ];
        }

        $texts = [];

        if ($service->service_date) {
            $texts[] = __('front/services.dashboard_date').": ".$service->service_date;
        }

        if ($service->service_starts) {
            $timing = $service->service_starts->format('H\hi');
            if ($service->service_ends) {
                $timing .= " - ".$service->service_ends->format('H\hi');
            }
            $texts[] = __('front/services.dashboard_timings').": ".$timing;
        }

        $location = $service->place ? __('front/services.dashboard_location').": ".$service->place->name : '';
        if ($service->room) {
            $location .= " - ".$service->room->name;
        }

        if ($location) {
            $texts[] = $location;
        }

        if ($service->description) {
            $texts[] = __('front/services.dashboard_description').": ".$service->description;
        }

        $texts[]  = __('front/services.dashboard_quantity').": ".$cartService->quantity;
        $totalTtc = $cartService->total_net + $cartService->total_vat;
        $texts[]  = __('front/services.dashboard_price').": ".$totalTtc." €".
            (
            $cartService->total_pec > 0
                ? ' <span style="text-decoration: line-through;">'.$cartService->unit_price * $cartService->quantity.' €</span>'
                : ''
            );

        return array_merge([
            'title'          => $service->title,
            'text'           => implode('<br>', $texts),
            'extra_badges'   => $extraBadges,
            'source'         => $source,
            'paid_by'        => $cartService->order->client()->names(),
            'order_id'       => $cartService->order->id,
            'event_id'       => $cartService->order->event_id,
            'total_pec'      => $cartService->total_pec,
            'unit_price'     => $cartService->unit_price,
            'total_price'    => $totalTtc,
            'deposit_amount' => $depositAmount,
            'deposit_paid'   => $depositPaid,
            'service_group'  => $serviceGroupId,
            'service_position' => $servicePosition,
        ], $badge);
    }
}
