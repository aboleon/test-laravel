<?php

namespace App\Accessors;

use App\Models\DictionnaryEntry;
use App\Models\Hotel;

class HotelServices
{
    public static function getServiceSelectableByIds(array $serviceIds): array
    {
        $services = DictionnaryEntry::whereIn('id', $serviceIds)->get();
        return $services->pluck('name', 'id')->toArray();
    }


    public static function getHotelServiceNames(Hotel $hotel)
    {
        return DictionnaryEntry::whereIn('id', $hotel->services)
            ->get()
            ->pluck('name')
            ->toArray();
    }


    public static function getServiceNameToFrontIcon()
    {
        return [
            'wifi' => 'bi bi-wifi',
            'restaurant' => 'fa-solid fa-utensils',
            'swimming-pool' => 'fa-solid fa-person-swimming',
            'piscine' => 'fa-solid fa-person-swimming',
            'spa' => 'fa-solid fa-spa',
        ];
    }

}