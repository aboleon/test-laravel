<?php

namespace App\Services\Availability\Repositories\Eloquent;

use App\Models\EventManager\Accommodation as Hotel;
use App\Models\EventManager\Accommodation\BlockedRoom;
use App\Services\Availability\Interfaces\BlockedRoomRepository;
use App\Services\Availability\ValueObjects\BookingData;
use App\Services\Availability\ValueObjects\DateRange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentBlockedRoomRepository implements BlockedRoomRepository
{
    public function getForHotel(Hotel $hotel, DateRange $dateRange): Collection
    {
        $query = BlockedRoom::query()
            ->where('event_accommodation_id', $hotel->id);

        $this->applyDateRangeFilter($query, $dateRange);

        return $query->get();
    }

    public function getForGroup(Hotel $hotel, int $groupId, DateRange $dateRange): Collection
    {
        return $this->getForHotel($hotel, $dateRange)
            ->filter(fn($blocked) => $blocked->event_group_id === $groupId);
    }

    public function getBlockedRoomsWithBookingData(Hotel $hotel, DateRange $dateRange): Collection
    {
        return $this->getForHotel($hotel, $dateRange)
            ->map(function ($blockedRoom) {
                $bookingData = new BookingData();
                return $bookingData
                    ->setQuantity($blockedRoom->quantity ?? 0)
                    ->setGroupId($blockedRoom->event_group_id)
                    ->setCancelled($blockedRoom->is_cancelled ?? false)
                    ->setTemp($blockedRoom->is_temp ?? false)
                    ->setAmended($blockedRoom->is_amended ?? false);
            });
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
