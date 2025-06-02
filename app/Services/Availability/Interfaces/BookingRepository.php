<?php

namespace App\Services\Availability\Interfaces;

use App\Models\EventManager\Accommodation as Hotel;
use App\Services\Availability\ValueObjects\DateRange;
use Illuminate\Support\Collection;

interface BookingRepository
{
    public function getForHotel(Hotel $hotel, DateRange $dateRange): Collection;
    public function getTemporaryForHotel(Hotel $hotel, DateRange $dateRange): Collection;
    public function getForGroup(Hotel $hotel, int $groupId, DateRange $dateRange): Collection;
}
