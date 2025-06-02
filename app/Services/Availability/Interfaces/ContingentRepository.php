<?php

namespace App\Services\Availability\Interfaces;

use App\Models\EventManager\Accommodation as Hotel;
use App\Services\Availability\ValueObjects\DateRange;
use Illuminate\Support\Collection;

interface ContingentRepository
{
    public function getForHotel(Hotel $hotel, DateRange $dateRange): Collection;
    public function getForRoomGroup(Hotel $hotel, int $roomGroupId, DateRange $dateRange): Collection;
}
