<?php

namespace App\Services\Pec;

class PecDistributionResult
{
    public const string COVERED_TOTAL = 'total';
    public const string COVERED_DISTRIBUTED = 'distributed';
    public const string COVERED_UNCOVERED = 'uncovered';

    private string $covered;
    private array $distribution;
    private array $overview;

    public function __construct(string $covered = self::COVERED_UNCOVERED, array $distribution = [], array $overview = [])
    {
        $this->covered = $covered;
        $this->distribution = $distribution;
        $this->overview = $overview;
    }

    public function getTotalCost(): int
    {
        $total = 0;

        $total += $this->getNetCost();

        $processingFees = $this->getProcessingFee();
        $total += (int)data_get($processingFees, 'cost', 0);


        return $total;
    }

    public function getNetCost(): int
    {
        $total = 0;

        $accommodation = $this->getAccommodation();
        $total += (int)data_get($accommodation, 'cost', 0);

        $services = collect($this->getServices());
        $total += $services->sum(function ($service) {
            return (int)data_get($service, 'cost.pec_allocation', 0) * (int)data_get($service, 'cost.quantity', 0);
        });

        return $total;
    }

    public function getCovered(): string
    {
        return $this->covered;
    }

    public function getDistribution(): array
    {
        return $this->distribution;
    }

    public function getServices(): array
    {
        return $this->distribution['services'] ?? [];
    }


    public function getAccommodation(): array
    {
        return $this->distribution['accommodation'] ?? [];
    }

    public function getProcessingFee(): array
    {
        return $this->distribution['processing_fees'] ?? [];
    }

    public function getOverview(): array
    {
        return $this->overview;
    }

    public function setCovered(string $covered): void
    {
        $this->covered = $covered;
    }

    public function setDistribution(array $distribution): void
    {
        $this->distribution = $distribution;
    }

    public function addOverview(array $overview): void
    {
        $this->overview[] = $overview;
    }

    public function isCovered(): bool
    {
        return $this->covered !== self::COVERED_UNCOVERED;
    }

    public function isNotCovered(): bool
    {
        return !$this->isCovered();
    }
}
