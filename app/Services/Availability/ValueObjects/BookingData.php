<?php

namespace App\Services\Availability\ValueObjects;


class BookingData
{
    private int $quantity = 0;
    private bool $onQuota = false;
    private ?int $participationType = null;
    private ?int $groupId = null;
    private bool $isTemp = false;
    private bool $isCancelled = false;
    private bool $isAmended = false;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name ?? null;
    }

    public function getRawOriginal($name)
    {
        return $this->$name ?? null;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function setOnQuota(bool $onQuota): self
    {
        $this->onQuota = $onQuota;
        return $this;
    }

    public function setParticipationType(?int $type): self
    {
        $this->participationType = $type;
        return $this;
    }

    public function setGroupId(?int $groupId): self
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function setTemp(bool $isTemp): self
    {
        $this->isTemp = $isTemp;
        return $this;
    }

    public function setCancelled(bool $isCancelled): self
    {
        $this->isCancelled = $isCancelled;
        return $this;
    }

    public function setAmended(bool $isAmended): self
    {
        $this->isAmended = $isAmended;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function isOnQuota(): bool
    {
        return $this->onQuota;
    }

    public function getParticipationType(): ?int
    {
        return $this->participationType;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function isTemp(): bool
    {
        return $this->isTemp;
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }

    public function isAmended(): bool
    {
        return $this->isAmended;
    }
}
