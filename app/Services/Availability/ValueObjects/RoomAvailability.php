<?php

namespace App\Services\Availability\ValueObjects;


class RoomAvailability
{
    private string $date;
    private int $roomGroupId;
    private int $totalCapacity = 0;
    private int $blocked = 0;
    private int $booked = 0;
    private int $tempBooked = 0;
    private array $groupBlocked = [];
    private array $participationTypeQuotas = [];

    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function setRoomGroupId(int $id): self
    {
        $this->roomGroupId = $id;
        return $this;
    }

    public function setTotalCapacity(int $capacity): self
    {
        $this->totalCapacity = $capacity;
        return $this;
    }

    public function setBlocked(int $blocked): self
    {
        $this->blocked = $blocked;
        return $this;
    }

    public function setBooked(int $booked): self
    {
        $this->booked = $booked;
        return $this;
    }

    public function setTempBooked(int $tempBooked): self
    {
        $this->tempBooked = $tempBooked;
        return $this;
    }

    public function setGroupBlocked(array $groupBlocked): self
    {
        $this->groupBlocked = $groupBlocked;
        return $this;
    }

    public function setParticipationTypeQuotas(array $quotas): self
    {
        $this->participationTypeQuotas = $quotas;
        return $this;
    }

    public function getAvailable(): int
    {
        $blocked = $this->blocked;
        foreach ($this->groupBlocked as $groupQuota) {
            $blocked += $groupQuota['total'] - ($groupQuota['booked'] ?? 0);
        }

        return max(0, $this->totalCapacity - $blocked - $this->booked - $this->tempBooked);
    }

    public function getGroupQuota(int $groupId): ?array
    {
        return $this->groupBlocked[$groupId] ?? null;
    }

    public function getParticipationTypeQuota(int $type): ?int
    {
        return $this->participationTypeQuotas[$type] ?? null;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'room_group_id' => $this->roomGroupId,
            'total_capacity' => $this->totalCapacity,
            'blocked' => $this->blocked,
            'booked' => $this->booked,
            'temp_booked' => $this->tempBooked,
            'available' => $this->getAvailable(),
            'group_quotas' => $this->groupBlocked,
            'participation_type_quotas' => $this->participationTypeQuotas
        ];
    }
}
