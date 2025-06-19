<?php

namespace App\Accessors\Front\Sellable;

use App\Accessors\EventContactAccessor;
use App\Enum\OrderSource;
use App\Models\EventContact;
use App\Models\Order;
use App\Models\Order\Cart\AccommodationCart;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class Accommodation
{
    public static function getAccommodationItems(EventContactAccessor|EventContact $accessor): Collection
    {
        // If EventContact is passed, create accessor (backward compatibility)
        if ($accessor instanceof EventContact) {
            $eventContactAccessor = new EventContactAccessor();
            $eventContactAccessor->setEventContact($accessor);
        } else {
            // Use the passed accessor directly
            $eventContactAccessor = $accessor;
        }

        $carts = $eventContactAccessor->getAccommodationCarts()->filter(fn($item) => empty($item['was_amended']));
        $ret   = [];

        // Original accommodation items
        $carts->each(function (Order $orderCart) use (&$ret) {
            $accommodations = $orderCart->accommodation;
            if ($accommodations->isEmpty()) {
                return null;
            }

            foreach ($accommodations as $cartAcc) {
                $ret[] = self::formatAccommodationItem($cartAcc, 'order');
            }
        });
        $attributions = $eventContactAccessor->accommodationAttributions();

        // Accommodation attributions
        $attributions->each(function ($attribution) use (&$ret) {
            $ret[] = self::formatAccommodationItem($attribution, 'attribution');
        });

        return collect($ret);
    }

    private static function formatAccommodationItem($item, string $source): array
    {
        $accompanying_details = '';
        $accompanying         = $item->order->accompanying->where('room_id', $item->room_id);
        $totalAccompanying    = $accompanying->sum('total');
        $comment              = '';
        $processing_fee_ttc   = 0;
        $nbPersons            = 1 + $totalAccompanying;
        $price                = 0;
        $error                = false;
        $badges               = [];
        $error_message        = '';

        if ($item->cancelled_at) {
            $badges['cancelled'] = ['class' => 'text-bg-danger', 'text' => __('front/order.cancelled')];
        }

        try {
            if ($source == OrderSource::ATTRIBUTION->value) {
                $date                                    = $item->configs['date'];
                $eventHotel                              = $item->shoppable->group->hotel;
                $hotelName                               = $item->shoppable->group->hotel->hotel->name ?? __('front/accommodation.no_hotel');
                $roomGroupName                           = strtoupper($item->shoppable->group->name ?? __('front/accommodation.no_room_group'));
                $roomName                                = $item->shoppable->room->name ?? __('front/accommodation.no_room');
                $priceFormatted                          = __('front/ui.free_of_charge');
                $badges[OrderSource::ATTRIBUTION->value] = ['class' => 'text-bg-warning', 'text' => __('front/ui.attribution_paid_by', ['payer' => $item->order->client()->names()])];
            } else {
                $eventHotel = $item->eventHotel;

                $date                 = $item->date->toDateString();
                $hotelName            = $eventHotel->hotel->name ?? __('front/accommodation.no_hotel');
                $roomGroupName        = strtoupper($item->roomGroup->name ?? __('front/accommodation.no_room_group'));
                $roomName             = $item->room->room->name ?? __('front/accommodation.no_room');
                $price                = $item->total_net + $item->total_vat;
                $priceFormatted       = $price.' €';
                $accompanying_details = self::getAccompanyingText($item, $accompanying);
                $comment              = self::getCommentsText($item);

                $processing_fee_ttc = $item->processing_fee_ttc / 100;
            }
        } catch (Throwable $e) {
            $error         = true;
            $error_message = 'Une erreur est survenue sur le traitement de #'.$item->id.', source: '.$source;
            Log::error($error_message);
            report($e);
        }

        try {
            $dateFormatted = Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
        } catch (Throwable) {
            $dateFormatted = 'NC';
        }


        if ( ! $error) {
            $texts = [
                "Le $dateFormatted",
                "Chambre $roomGroupName - $roomName",
                "Prix : $priceFormatted",
                "Nombre de personnes : $nbPersons",
            ];

            if ($accompanying_details) {
                $texts[] = "Détails accompagnants : $accompanying_details";
            }
            if ($comment) {
                $texts[] = "Commentaire : $comment";
            }
            if ($processing_fee_ttc) {
                $texts[] = "Frais de dossier : $processing_fee_ttc €";
            }


            $parsed = [
                'error'                => (int)$error,
                'title'                => __('front/order.overnight_at', ['number' => $item->quantity, 'overnight' => trans_choice('front/order.overnight', $item->quantity), 'hotel' => $hotelName]),
                'text'                 => implode('<br>', $texts),
                'hotel'                => $hotelName,
                'event_hotel_id'       => $eventHotel->id,
                'hotel_id'             => $eventHotel->hotel->id,
                'roomgroup_name'       => $roomGroupName,
                'room_name'            => $roomName,
                'roomgroup'            => $item->room_group_id ?? null,
                'price'                => $price,
                'date'                 => $date,
                'has_amended'          => $item->order->amended_order_id ?? null,
                'amend_type'           => $item->order->amend_type ?? null,
                'was_amended'          => $item->order->amended_by_order_id ?? null,
                'cancelled_at'         => $item->cancelled_at ? $item->cancelled_at->format('d/m/Y') : null,
                'cancellation_request' => $item->cancellation_request ? $item->cancellation_request->format('d/m/Y') : null,
                'order_id'             => $item->order_id ?? null,
                'badge'                => $badges,
                'source'               => $source,
                'paid_by'              => $item->order->client()->names(),
                'event_id'             => $item->order->event_id,
                'accompagnant'         => $accompanying_details,
                'nbre_accompagnant'    => $totalAccompanying,
            ];

            /*d(
                $x
            );*/

            return $parsed;
        }

        return [
            'error' => 1,
            'text'  => $error_message,
        ];
    }

    public static function getCommentsText(AccommodationCart $accommodationCart): string
    {
        $comment_bo = $accommodationCart->order->roomnotes->where('room_id', $accommodationCart->room_id);
        if ( ! $comment_bo->count()) {
            return '';
        }

        return $comment_bo->pluck('note')->join(', ');
    }

    public static function getAccompanyingText(AccommodationCart $accommodationCart, ?Collection $accompanying = null): string
    {
        if ( ! $accompanying) {
            $accompanying = $accommodationCart->order->accompanying->where('room_id', $accommodationCart->room_id);
        }

        return trim(($accommodationCart->accompanying_details ? $accommodationCart->accompanying_details.', ' : '').$accompanying?->pluck('names')->join(', '));
    }

    public static function getAccommodationCheckIns(EventContactAccessor|EventContact $accessor): Collection
    {
        // Get all accommodation items
        $accommodationItems = self::getAccommodationItems($accessor);

        // Filter out errors and cancelled items
        $validItems = $accommodationItems->filter(function ($item) {
            return ! $item['error'] && ! $item['cancelled_at'];
        });

        // Group by hotel_id
        $groupedByHotel = $validItems->groupBy('hotel_id');

        // Process each hotel group
        $checkIns = $groupedByHotel->map(function ($hotelItems, $hotelId) {
            // Get hotel name from first item
            $hotelName = $hotelItems->first()['hotel'];

            // Get all unique dates for this hotel and sort them
            $dates = $hotelItems
                ->pluck('date')
                ->unique()
                ->map(fn($date) => Carbon::parse($date))
                ->sort()
                ->values();

            // Find continuous date ranges
            $ranges     = [];
            $rangeStart = $dates->first();
            $rangeEnd   = $dates->first();

            for ($i = 1; $i < $dates->count(); $i++) {
                // If the current date is consecutive to the previous one
                if ($dates[$i]->diffInDays($rangeEnd) === 1) {
                    $rangeEnd = $dates[$i];
                } else {
                    // Gap found, save current range and start a new one
                    $ranges[]   = [
                        'check_in'  => $rangeStart,
                        'check_out' => $rangeEnd->copy()->addDay(),
                    ];
                    $rangeStart = $dates[$i];
                    $rangeEnd   = $dates[$i];
                }
            }

            // Add the last range
            $ranges[] = [
                'check_in'  => $rangeStart,
                'check_out' => $rangeEnd->copy()->addDay(),
            ];

            // Return all ranges for this hotel
            return collect($ranges)->map(function ($range) use ($hotelId, $hotelName, $hotelItems) {
                // Get items for this date range
                $rangeItems = $hotelItems->filter(function ($item) use ($range) {
                    $itemDate = Carbon::parse($item['date']);

                    return $itemDate->gte($range['check_in']) && $itemDate->lt($range['check_out']);
                });

                // Aggregate accompanying data for this range
                $allAccompagnants   = $rangeItems->pluck('accompagnant')->filter()->unique();
                $totalAccompagnants = $rangeItems->sum('nbre_accompagnant');

                return [
                    'hotel_id'            => $hotelId,
                    'hotel_name'          => $hotelName,
                    'check_in'            => $range['check_in']->format('Y-m-d'),
                    'check_out'           => $range['check_out']->format('Y-m-d'),
                    'check_in_formatted'  => $range['check_in']->format('d/m/Y'),
                    'check_out_formatted' => $range['check_out']->format('d/m/Y'),
                    'nights'              => $range['check_in']->diffInDays($range['check_out']),
                    'accompagnant'        => $allAccompagnants->implode(', '),
                    'nbre_accompagnant'   => $totalAccompagnants,
                ];
            });
        });

        // Flatten the collection and sort by check-in date
        return $checkIns->flatten(1)->sortBy('check_in')->values();
    }
}
