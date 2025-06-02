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
    public static function getAccommodationItems(EventContact $eventContact): Collection
    {
        $eventContactAccessor = new EventContactAccessor();
        $eventContactAccessor->setEventContact($eventContact);

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
        $nbPersons            = ($item->quantity ?? 0) + $totalAccompanying;
        $price                = 0;
        $error                = false;
        $badges               = [];
        $error_message = '';

        if ($item->cancelled_at) {
            $badges['cancelled'] = ['class' => 'text-bg-danger', 'text' => __('front/order.cancelled')];
        }

        try {
            if ($source == OrderSource::ATTRIBUTION->value) {
                $date                                    = $item->configs['date'];
                $hotelName                               = $item->shoppable->group->hotel->hotel->name ?? __('front/accommodation.no_hotel');
                $roomGroupName                           = strtoupper($item->shoppable->group->name ?? __('front/accommodation.no_room_group'));
                $roomName                                = $item->shoppable->room->name ?? __('front/accommodation.no_room');
                $priceFormatted                          = __('front/ui.free_of_charge');
                $badges[OrderSource::ATTRIBUTION->value] = ['class' => 'text-bg-warning', 'text' => __('front/ui.attribution_paid_by', ['payer' => $item->order->client()->names()])];
            } else {
                $date                 = $item->date->toDateString();
                $hotelName            = $item->eventHotel->hotel->name ?? __('front/accommodation.no_hotel');
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


            $parsed =  [
                'error'                => (int)$error,
                'title'                => __('front/order.overnight_at', ['number' => $item->quantity, 'overnight' => trans_choice('front/order.overnight', $item->quantity), 'hotel' => $hotelName]),
                'text'                 => implode('<br>', $texts),
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
}
