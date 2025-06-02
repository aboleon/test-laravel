<?php

namespace App\Accessors\EventManager;


use App\Models\Event;
use App\Models\EventManager\Accommodation;
use App\Models\EventManager\Accommodation\RoomGroup;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\Order\StockTemp;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Accommodations
{
    public static function hotelLabelsWithStatus(Event $event, bool $status = true): array
    {
        return $event->accommodation->load('hotel')->mapWithKeys(fn($item) => [
            $item->id => '<span class="hotel-name d-block">' . $item->hotel->name . ' ' .
                (
                $item->hotel->stars ? $item->hotel->stars . '*'
                    : ''
                ) . '</span>' .
                (
                !empty($item->title)
                    ? '<span class="subtitle text-secondary"> / ' . $item->title . '</span>'
                    : ''
                ) .
                (
                $status
                    ? '<span class="d-block status text-secondary">' . ($item->published ? ' en' : ' hors') . ' ligne' . '</span>'
                    : ''
                )
        ])->toArray();
    }

    public static function getBookingsCountForRoomGroup(int $roomGroupId): int
    {
       return AccommodationCart::where('room_group_id', $roomGroupId)
            ->whereNotIn('id', function ($query) {
                $query->select('amended_cart_id')
                    ->from('order_cart_accommodation')
                    ->whereNotNull('amended_cart_id');
            })
            ->whereNotIn('order_id', function ($query) {
                $query->select('id')
                    ->from('orders')
                    ->whereNotNull('amended_by_order_id')
                    ->where('amend_type', 'order');
            })
            ->count();
    }

}
