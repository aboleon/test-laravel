<?php

namespace App\Services\Availability\ValueObjects;


use Carbon\Carbon;
use Carbon\CarbonPeriod;
use InvalidArgumentException;

class DateRange
{
    private ?string $startDate = null;
    private ?string $endDate = null;
    private ?string $singleDate = null;
    private array $dateRange = [];

    public function setDate(string $date): self
    {
        $this->singleDate = $date;
        $this->startDate = null;
        $this->endDate = null;
        $this->generateDateRange();
        return $this;
    }

    public function setStartDate(string $date): self
    {
        $this->startDate = $date;
        $this->singleDate = null;
        $this->validate();
        $this->generateDateRange();
        return $this;
    }

    public function setEndDate(string $date): self
    {
        $this->endDate = $date;
        $this->singleDate = null;
        $this->validate();
        $this->generateDateRange();
        return $this;
    }

    private function validate(): void
    {
        if ($this->startDate && $this->endDate && $this->startDate >= $this->endDate) {
            throw new InvalidArgumentException('Invalid date range');
        }
    }

    private function generateDateRange(): void
    {
        $this->dateRange = [];

        if ($this->singleDate) {
            $this->dateRange = [$this->singleDate];
            return;
        }

        if ($this->startDate && $this->endDate) {
            $period = CarbonPeriod::create(
                $this->startDate,
                '1 day',
                Carbon::parse($this->endDate)->subDay()
            );

            foreach ($period as $date) {
                $this->dateRange[] = $date->format('Y-m-d');
            }
        }
    }

    public function getDateRange(): array
    {
        return $this->dateRange;
    }

    public function isSingleDate(): bool
    {
        return !is_null($this->singleDate);
    }

    public function getSingleDate(): ?string
    {
        return $this->singleDate;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }
}
