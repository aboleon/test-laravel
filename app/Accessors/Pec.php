<?php

namespace App\Accessors;

use Illuminate\Support\Facades\DB;

class Pec
{
    public static function accountHasAlreadyPec(int $user_id): bool
    {
        return DB::table('event_grant_funding_records')
            ->join('orders', 'event_grant_funding_records.order_id', '=', 'orders.id')
            ->join('events_contacts', 'orders.client_id', '=', 'events_contacts.user_id')
            ->where('events_contacts.user_id', $user_id)
            ->where('orders.client_type', 'contact')
            ->exists();
    }

    public static function getPecDistributedForHotelId(int $event_accommodation_id)
    {
        return DB::table('pec_distribution')
            ->join('order_cart_accommodation', 'pec_distribution.order_id', '=', 'order_cart_accommodation.order_id')
            ->where('order_cart_accommodation.event_hotel_id', $event_accommodation_id)
            ->where('pec_distribution.type', 'accommodation')
            ->select(
                'pec_distribution.order_id',
                'order_cart_accommodation.id as order_cart_accommodation_id',
                'order_cart_accommodation.room_group_id',
                'pec_distribution.quantity',
                'order_cart_accommodation.date',
            )
            ->get()
            ->map(fn($item) => (array)$item)->toArray();
    }


}
