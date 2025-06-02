<?php

namespace App\Printers\Event;

use Illuminate\Database\Eloquent\Collection;

class Accommodation
{
    public static function simpleRecap(Collection $accommodationCart, bool $showPec = false)
    {
        $grouped  = $accommodationCart->groupBy(['event_hotel_id', 'room_id']);
        $hasPec   = false;
        $pecLabel = '';

        return $grouped->map(function ($hotelGroup, $eventHotelId) use (&$hasPec, $showPec, $pecLabel) {
            return $hotelGroup->map(function ($roomGroup, $roomId) use ($eventHotelId, &$hasPec, $showPec, $pecLabel) {
                $roomGroups = $roomGroup->first()->eventHotel->roomGroups->pluck('name', 'id')->toArray();

                $minDate = $roomGroup->min('date');
                $maxDate = $roomGroup->max('date');


                $maxDateReadable = $maxDate->clone()->addDay()->format('d/m/Y');

                if ($minDate == $maxDate) {
                    // Same day
                    $dateString = $minDate->format('d/m/Y');
                } elseif ($minDate->format('Y-m') === $maxDate->format('Y-m')) {
                    // Same month and year, different day
                    $dateString = $minDate->format('d').' au '.$maxDateReadable;
                } elseif ($minDate->format('Y') === $maxDate->format('Y')) {
                    // Same year, different month
                    $dateString = $minDate->format('d/m').' au '.$maxDateReadable;
                } else {
                    // Different year
                    $dateString = $minDate->format('d/m/Y').' au '.$maxDateReadable;
                }

                if ( ! $hasPec) {
                    $hasPec = (bool)$roomGroup->where('total_pec', '>', 0)->count();
                }

                if ($showPec && $hasPec) {
                    $pecLabel = view('components.pec-mark')->render();
                }

                $attributions = view('events.manager.dashboard.accommodation-attributions')->with(['accommodationCart' => $roomGroup->first(), 'roomId' => $roomId])->render();
                $accompanying_notes = view('events.manager.dashboard.accompanying-notes-accommodation')->with(['accommodationCart' => $roomGroup->first(), 'roomId' => $roomId])->render();
                $room_notes = view('events.manager.dashboard.room-notes-accommodation')->with(['accommodationCart' => $roomGroup->first(), 'roomId' => $roomId])->render();

                return ($dateString." à ".$roomGroup->first()->eventHotel->hotel->name." dans ".$roomGroup->first()->room->room->name.' / '
                        .($roomGroups[$roomGroup->first()->room->room_group_id] ?? 'Inconnu '.($roomGroup->first()->room->room_group_id))).$pecLabel.$attributions.$accompanying_notes.$room_notes;
            });
        })->flatten()->values()->all();
    }
}
