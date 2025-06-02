<?php

namespace App\Services\Availability\Services;

use App\Services\Availability\ValueObjects\RoomAvailability;
use Illuminate\Support\Collection;

class AvailabilityCalculator
{
    public function calculateRoomAvailability(
        object $contingent,
        Collection $blockedRooms,
        Collection $bookings,
        Collection $tempBookings,
        ?int $participationType = null,
        ?int $groupId = null
    ): RoomAvailability {
        $roomAvailability = new RoomAvailability();

        $blocked = $this->calculateBlocked($blockedRooms, $contingent->getRawOriginal('date'), $contingent->room_group_id);
        $booked = $this->calculateBookings($bookings, $contingent->getRawOriginal('date'), $contingent->room_group_id);
        $tempBooked = $this->calculateTempBookings($tempBookings, $contingent->getRawOriginal('date'), $contingent->room_group_id);

        $roomAvailability
            ->setDate($contingent->getRawOriginal('date'))
            ->setRoomGroupId($contingent->room_group_id)
            ->setTotalCapacity($contingent->total)
            ->setBlocked($blocked)
            ->setBooked($booked)
            ->setTempBooked($tempBooked);

        if ($groupId) {
            $groupQuotas = [
                $groupId => $this->calculateGroupQuota(
                    $blockedRooms,
                    $bookings,
                    $tempBookings,
                    $contingent->getRawOriginal('date'),
                    $contingent->room_group_id,
                    $groupId
                )
            ];
            $roomAvailability->setGroupBlocked($groupQuotas);
        }

        if ($participationType) {
            $typeQuotas = [
                $participationType => $this->calculateParticipationTypeQuota(
                    $blockedRooms,
                    $bookings,
                    $tempBookings,
                    $contingent->getRawOriginal('date'),
                    $contingent->room_group_id,
                    $participationType
                )
            ];
            $roomAvailability->setParticipationTypeQuotas($typeQuotas);
        }

        return $roomAvailability;
    }

    private function calculateBlocked(Collection $blockedRooms, string $date, int $roomGroupId): int
    {
        return $blockedRooms
            ->filter(fn($block) =>
                $block->getRawOriginal('date') === $date &&
                $block->room_group_id === $roomGroupId
            )
            ->sum('total');
    }

    private function calculateBookings(Collection $bookings, string $date, int $roomGroupId): int
    {
        return $bookings
            ->filter(fn($booking) =>
                $booking->getRawOriginal('date') === $date &&
                $booking->room_group_id === $roomGroupId &&
                !$booking->isCancelled()
            )
            ->sum('quantity');
    }

    private function calculateTempBookings(Collection $tempBookings, string $date, int $roomGroupId): int
    {
        return $tempBookings
            ->filter(fn($booking) =>
                $booking->getRawOriginal('date') === $date &&
                $booking->room_group_id === $roomGroupId
            )
            ->sum('quantity');
    }

    private function calculateGroupQuota(
        Collection $blockedRooms,
        Collection $bookings,
        Collection $tempBookings,
        string $date,
        int $roomGroupId,
        int $groupId
    ): array {
        $blocked = $blockedRooms
            ->filter(fn($block) =>
                $block->getRawOriginal('date') === $date &&
                $block->room_group_id === $roomGroupId &&
                $block->event_group_id === $groupId
            )
            ->sum('total');

        $booked = $bookings
            ->filter(fn($booking) =>
                $booking->getRawOriginal('date') === $date &&
                $booking->room_group_id === $roomGroupId &&
                $booking->getGroupId() === $groupId &&
                $booking->isOnQuota()
            )
            ->sum('quantity');

        $tempBooked = $tempBookings
            ->filter(fn($booking) =>
                $booking->getRawOriginal('date') === $date &&
                $booking->room_group_id === $roomGroupId &&
                $booking->getGroupId() === $groupId &&
                $booking->isOnQuota()
            )
            ->sum('quantity');

        return [
            'total' => $blocked,
            'booked' => $booked,
            'temp_booked' => $tempBooked,
            'available' => max(0, $blocked - $booked - $tempBooked)
        ];
    }

    private function calculateParticipationTypeQuota(
        Collection $blockedRooms,
        Collection $bookings,
        Collection $tempBookings,
        string $date,
        int $roomGroupId,
        int $participationType
    ): int {
        $blocked = $blockedRooms
            ->filter(fn($block) =>
                $block->getRawOriginal('date') === $date &&
                $block->room_group_id === $roomGroupId &&
                in_array($participationType, explode(',', $block->participation_type))
            )
            ->sum('total');

        $booked = $bookings
            ->filter(fn($booking) =>
                $booking->getRawOriginal('date') === $date &&
                $booking->room_group_id === $roomGroupId &&
                $booking->getParticipationType() === $participationType
            )
            ->sum('quantity');

        $tempBooked = $tempBookings
            ->filter(fn($booking) =>
                $booking->getRawOriginal('date') === $date &&
                $booking->room_group_id === $roomGroupId &&
                $booking->getParticipationType() === $participationType
            )
            ->sum('quantity');

        return max(0, $blocked - $booked - $tempBooked);
    }
}
