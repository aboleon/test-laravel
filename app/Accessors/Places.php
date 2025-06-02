<?php

namespace App\Accessors;

use App\Models\Place;
use MetaFramework\Accessors\Countries;
use Throwable;

class Places
{
    public static function selectableArray(): array
    {
        return Place::select('places.id', 'places.name', 'b.locality', 'b.country_code')
            ->join('place_addresses as b', 'b.place_id', '=', 'places.id')
            ->join('countries as c', 'c.code', '=', 'country_code')
            ->orderBy('places.name', 'asc')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->id => $item->name . ', ' . $item->locality . ', ' . Countries::getCountryNameByCode($item->country_code)
            ])
            ->toArray();
    }

    public static function simpleSelectableArray(): array
    {
        return Place::select('id', 'name')
            ->get()
            ->sortBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }


    public static function getSearchResults(string $searchTerm = ""): array
    {
        $query = Place::where('name', 'LIKE', '%' . $searchTerm . '%');
        $places = $query->get();

        return $places->map(function ($place) {
            return [
                'value' => $place->name,
                'text' => $place->name,
            ];
        })->toArray();
    }

    public static function selectableRoomsForPlace(int $place_id)
    {
        try {
            return Place::findOrFail($place_id)->load('rooms')->rooms->pluck('name', 'id')->toArray();
        } catch (Throwable) {
            return [];
        }
    }
}
