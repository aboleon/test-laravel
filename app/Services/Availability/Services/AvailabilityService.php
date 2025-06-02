<?php

namespace App\Services\Availability\Services;

use App\Models\EventManager\Accommodation as Hotel;
use App\Services\Availability\Interfaces\BlockedRoomRepository;
use App\Services\Availability\Interfaces\BookingRepository;
use App\Services\Availability\Interfaces\ContingentRepository;
use App\Services\Availability\ValueObjects\DateRange;
use Illuminate\Support\Collection;

class AvailabilityService
{
    private Hotel $hotel;
    private BookingRepository $bookingRepo;
    private ContingentRepository $contingentRepo;
    private BlockedRoomRepository $blockedRepo;
    private AvailabilityCalculator $calculator;

    private DateRange $dateRange;
    private Collection $contingent;
    private Collection $blockedRooms;
    private Collection $bookings;
    private Collection $tempBookings;

    public static function for(Hotel $hotel): self
    {
        return (new self())
            ->setHotel($hotel)
            ->setRepositories();
    }

    private function setHotel(Hotel $hotel): self
    {
        $this->hotel = $hotel;
        return $this;
    }

    private function setRepositories(): self
    {
        $this->bookingRepo = app(BookingRepository::class);
        $this->contingentRepo = app(ContingentRepository::class);
        $this->blockedRepo = app(BlockedRoomRepository::class);
        $this->calculator = app(AvailabilityCalculator::class);
        return $this;
    }

    public function forDateRange(DateRange $dateRange): self
    {
        $this->dateRange = $dateRange;
        return $this;
    }

    public function calculate(
        ?int $roomGroupId = null,
        ?int $participationType = null,
        ?int $groupId = null
    ): Collection {
        $this->loadData($roomGroupId);

        return $this->contingent
            ->map(fn($contingent) => $this->calculator->calculateRoomAvailability(
                $contingent,
                $this->blockedRooms,
                $this->bookings,
                $this->tempBookings,
                $participationType,
                $groupId
            ));
    }
    public function getAvailability(): array
    {
        $availability = [];

        foreach ($this->calculate() as $roomAvailability) {
            $data = $roomAvailability->toArray();
            $availability[$data['date']][$data['room_group_id']] = $data['available'];
        }

        return array_filter($availability);
    }


    private function loadData(?int $roomGroupId): void
    {
        $this->contingent = $roomGroupId
            ? $this->contingentRepo->getForRoomGroup($this->hotel, $roomGroupId, $this->dateRange)
            : $this->contingentRepo->getForHotel($this->hotel, $this->dateRange);

        $this->blockedRooms = $this->blockedRepo->getForHotel($this->hotel, $this->dateRange);
        $this->bookings = $this->bookingRepo->getForHotel($this->hotel, $this->dateRange);
        $this->tempBookings = $this->bookingRepo->getTemporaryForHotel($this->hotel, $this->dateRange);
    }

}
