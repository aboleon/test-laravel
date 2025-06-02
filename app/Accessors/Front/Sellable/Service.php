<?php

namespace App\Accessors\Front\Sellable;

use App\Accessors\EventContactAccessor;
use App\Accessors\Order\Cart\ServiceCarts;
use App\Models\EventContact;
use App\Models\Order\Cart\ServiceCart;
use Illuminate\Support\Collection;

class Service
{

    public static function getServiceItems(EventContact $eventContact): Collection
    {
        // Fetch service carts for the event contact
        $cartServices = ServiceCarts::getServiceCartsByEventContact($eventContact);

        // Initialize EventContactAccessor to handle attributions
        $eventContactAccessor = (new EventContactAccessor())->setEventContact($eventContact);

        // Fetch attributed services
        $attributedServices = $eventContactAccessor->serviceAttributions()->load('order');

        // Map original service carts
        $mappedServices = $cartServices->map(function (ServiceCart $cartService) use ($eventContact) {
            return self::formatServiceItem($cartService, $eventContact, 'order');
        });

        // Map attributed services
        $mappedAttributions = $attributedServices->map(function ($attribution) use ($eventContact) {
            // Assuming `$attribution` has `service`, `quantity`, and `price` fields
            $service = $attribution->shoppable;

            $quantity = $attribution->quantity ?? 1;

            return [
                'title' => $service->title ?? __('front/services.dashboard_no_title'),
                'text' => implode('<br>', [
                    __('front/services.dashboard_quantity') . ": " . $quantity,
                    __('front/services.dashboard_price') . ": ". __('front/ui.free_of_charge')
                ]),
                'extra_badges' => [],
                'badge' => [
                    'class' => 'text-bg-warning rounded-pill',
                    'text' => __('front/ui.attribution_paid_by', ['payer' => $attribution->order->client()->names()]),
                ],
                'source' => 'attribution',
                'paid_by' => $attribution->order->client()->names(),
                'order_id' => $attribution->order->id,
                'event_id' => $attribution->order->event_id,
            ];
        });

        // Combine original and attributed services
        return $mappedServices->merge($mappedAttributions);
    }

// Helper method to format service items
    private static function formatServiceItem(ServiceCart $cartService, EventContact $eventContact, string $source): array
    {
        $service = $cartService->service;
        $badge = [];
        $extraBadges = [];

        if ($cartService->order->client_id !== $eventContact->user_id) {
            $payerName = $cartService->order->client()->names();
            $badge = [
                'badge' => [
                    'class' => 'text-bg-purple rounded-pill',
                    'text' => __('front/ui.paid_by', ['payer' => $payerName]),
                ],
            ];
        }

        if ($cartService->cancelled_at) {
            $extraBadges[] = [
                'class' => 'text-bg-danger rounded-pill',
                'text' => __('front/order.cancelled'),
            ];
        }

        $texts = [];

        if ($service->service_date) {
            $texts[] = __('front/services.dashboard_date') . ": " . $service->service_date;
        }

        if ($service->service_starts) {
            $timing = $service->service_starts->format('H\hi');
            if ($service->service_ends) {
                $timing .= " - " . $service->service_ends->format('H\hi');
            }
            $texts[] = __('front/services.dashboard_timings') . ": " . $timing;
        }

        $location = $service->place ? __('front/services.dashboard_location') . ": " . $service->place->name : '';
        if ($service->room) {
            $location .= " - " . $service->room->name;
        }

        if ($location) {
            $texts[] = $location;
        }

        if ($service->description) {
            $texts[] = __('front/services.dashboard_description') . ": " . $service->description;
        }

        $texts[] = __('front/services.dashboard_quantity') . ": " . $cartService->quantity;
        $totalTtc = $cartService->total_net + $cartService->total_vat;
        $texts[] = __('front/services.dashboard_price') . ": " . $totalTtc . " €";

        return array_merge([
            'title' => $service->title,
            'text' => implode('<br>', $texts),
            'extra_badges' => $extraBadges,
            'source' => $source,
        ], $badge);
    }


}
