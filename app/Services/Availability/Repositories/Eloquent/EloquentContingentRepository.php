<?php

namespace App\Services\Availability\Repositories\Eloquent;

use App\Models\EventManager\Accommodation as Hotel;
use App\Services\Availability\Interfaces\ContingentRepository;
use App\Services\Availability\ValueObjects\DateRange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentContingentRepository implements ContingentRepository
{
    public function getForHotel(Hotel $hotel, DateRange $dateRange): Collection
    {
        $query = $hotel->contingent();

        $this->applyDateRangeFilter($query->getQuery(), $dateRange);

        return $query
            ->with('configs.rooms')
            ->orderBy('date')
            ->get();
    }

    public function getForRoomGroup(Hotel $hotel, int $roomGroupId, DateRange $dateRange): Collection
    {
        return $this->getForHotel($hotel, $dateRange)
            ->filter(fn($contingent) => $contingent->room_group_id === $roomGroupId);
    }

    private function applyDateRangeFilter(Builder $query, DateRange $dateRange): void
    {
        if ($dateRange->isSingleDate()) {
            $query->where('date', $dateRange->getSingleDate());
            return;
        }

        if ($dateRange->getStartDate()) {
            $query->where('date', '>=', $dateRange->getStartDate());
        }

        if ($dateRange->getEndDate()) {
            $query->where('date', '<', $dateRange->getEndDate());
        }
    }
}
