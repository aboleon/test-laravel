<?php

namespace App\Services\Pec;

use App\Enum\AmountType;
use App\Models\EventContact;
use App\Models\EventManager\Sellable;
use App\Services\Grants\ParsedGrant;
use Illuminate\Support\Collection;

class PecFinder
{
    private array $services = [];
    private array $servicesTresholds = [];
    private Collection $grants;
    private int|float $accommodationTotal = 0;
    private int|float $serviceTotal = 0;
    private int|float $transportFeesWithTax = 0;
    private int|float $transportFeesWhitoutTax = 0;
    private bool $forTransport = false;
    private bool $withProcessingFee = false;
    private bool $processingFeeProcessed = false;
    private PecDistributionResult $result;
    private EventContact $eventContact;
    private array $bookedServices = [];
    private mixed $accommodationEligibles = [];

    public function __construct()
    {
        $this->result = new PecDistributionResult(
            PecDistributionResult::COVERED_UNCOVERED,
            [
                'services'        => [],
                'accommodation'   => [],
                'processing_fees' => [],
            ],
            [],
        );
    }

    public function setServices(array $services): self
    {
        $this->services = $services;
        if ($this->services) {
            $this->servicesTresholds = Sellable::whereIn('id', array_keys($this->services))->pluck('pec_max_pax', 'id')->filter(fn($item) => (int)$item > 0)->toArray();

            if ($this->servicesTresholds) {
                $this->getBookedServices();
            }
        }

        return $this;
    }

    private function getBookedServices(): self
    {
        $this->bookedServices = $this->eventContact->pecDistributions->where('type', PecType::SERVICE->value)->pluck('quantity', 'shoppable_id')->toArray();

        return $this;
    }

    public function excludeGrant(int $grant_id): self
    {
        $this->grants->forget($grant_id);

        return $this;
    }

    public function setGrants(Collection $grants): self
    {
        $this->grants = $grants;

        return $this;
    }

    public function getGrants(): Collection
    {
        return $this->grants;
    }

    public function setAccommodationTotal(int|float $total): self
    {
        $this->accommodationTotal = $total;

        return $this;
    }

    public function setServiceTotal(int|float $total): self
    {
        $this->serviceTotal = $total;

        return $this;
    }

    public function getAccommodationTotal(): int|float
    {
        return $this->accommodationTotal;
    }

    public function askForProcessingFees(bool $ask): self
    {
        $this->withProcessingFee = $ask;

        return $this;
    }

    public function getServiceTotal(): int|float
    {
        // If it set through SetServiceTotal
        if ($this->serviceTotal != 0) {
            return $this->serviceTotal;
        }

        $this->serviceTotal = 0;

        if ( ! $this->services) {
            return $this->serviceTotal;
        }

        foreach ($this->services as $entry) {
            $this->serviceTotal += $entry['unit_price'] * $entry['quantity'];
        }

        return $this->serviceTotal;
    }

    public function filterGrants(): PecDistributionResult
    {
        if ($this->forTransport) {
            return $this->transportGrant();
        }

        if ($this->servicesTresholds) {
            foreach ($this->servicesTresholds as $service_id => $limit) {
                $qtyAsked                                = $this->services[$service_id]['quantity'];
                $bookedCounter                           = $this->bookedServices[$service_id] ?? 0;
                $available_within_limit                  = $limit - $bookedCounter;
                $this->services[$service_id]['quantity'] = min($qtyAsked, $available_within_limit);
            }
        }

        $totalServiceCost = $this->getServiceTotal();

        $totalAccommodationCost = $this->getAccommodationTotal();
        $totalCost              = $totalServiceCost + $totalAccommodationCost;
        $availableBudget        = $this->grants->sum(function ($grant) {
            return $grant->getAvailableBudget($this->withProcessingFee);
        });

        if ( ! $totalCost || $availableBudget < $totalCost) {
            return new PecDistributionResult(PecDistributionResult::COVERED_UNCOVERED);
        }

        $covered = $this->findSingleCoveringGrant($totalServiceCost, $totalAccommodationCost);
        if ($covered) {
            $this->result->setCovered(PecDistributionResult::COVERED_TOTAL);
            $this->result->setDistribution($covered['distribution']);
            $this->result->addOverview($covered['overview']);
        } else {
            $sortedGrants         = $this->grants->sortByDesc('score');
            $serviceCovered       = $this->distributeCosts($totalServiceCost, 'services', $sortedGrants);
            $accommodationCovered = $totalAccommodationCost == 0 || $this->distributeCosts($totalAccommodationCost, 'accommodation', $sortedGrants);

            if ($serviceCovered && $accommodationCovered) {
                $this->result->setCovered(PecDistributionResult::COVERED_DISTRIBUTED);
            } else {
                return new PecDistributionResult(PecDistributionResult::COVERED_UNCOVERED);
            }

            if ($this->withProcessingFee && ! $this->processingFeeProcessed) {
                $processingFeeCovered = $this->distributeProcessingFees($sortedGrants);
                if ( ! $processingFeeCovered) {
                    return new PecDistributionResult(PecDistributionResult::COVERED_UNCOVERED);
                }
            }
        }

        return $this->result;
    }

    private function findSingleCoveringGrant(int|float $serviceCost, int|float $accommodationCost): ?array
    {
        $totalCost = $serviceCost + $accommodationCost;

        foreach ($this->grants as $grant) {
            $available = $grant->getAvailableBudget($this->withProcessingFee);

            if ($available >= $totalCost) {
                $distribution = [
                    'services'      => $this->distributeItems($grant, $this->services),
                    'accommodation' => $this->setAccommodationEvalutation($grant, $accommodationCost),
                ];

                if ($this->withProcessingFee) {
                    $distribution['processing_fees'] = $this->getGrantProcessingFees($grant);
                }

                return [
                    'distribution' => $distribution,
                    'overview'     => $this->setOverview($grant, $totalCost),
                ];
            }
        }

        return null;
    }

    private function getGrantProcessingFees($grant): array
    {
        return [
            'cost'     => $grant->config['pec_fee'],
            'grant_id' => $grant->id,
            'vat_id'   => $grant->event_pec_config['processing_fees_vat_id'],
            'title'    => $grant->config['title'],
        ];
    }

    private function setOverview($grant, $totalCost): array
    {
        return [
            'grant_id' => $grant->id,
            'title'    => $grant->config['title'],
            'before'   => $grant->budget['available'],
            'after'    => $grant->budget['available'] - $totalCost - ($this->withProcessingFee ? $grant->config['pec_fee'] : 0),
        ];
    }

    private function setAccommodationEvalutation($grant, $accommodationCost): array
    {
        return $accommodationCost > 0
            ? [
                'cost'     => $accommodationCost,
                'grant_id' => $grant->id,
                'title'    => $grant->config['title'],
                'items'    => $this->accommodationEligibles,
            ]
            : [];
    }

    private function findTransportGrant(): ?array
    {
        foreach ($this->grants->reject(fn($item) => $item->budget['allow_transport_refund'] != 1) as $grant) {
            $available = $grant->getAvailableBudget($this->withProcessingFee);
            $totalCost = $grant['budget']['type'] == AmountType::TAX->value ? $this->transportFeesWithTax : $this->transportFeesWhitoutTax;

            if ($available >= $totalCost) {
                $acceptable = min($totalCost, $grant['budget']['transport_max']);

                $distribution = [
                    'transport' => [
                        'cost'          => $acceptable,
                        'cost_original' => $totalCost,
                        'cost_max'      => $grant['budget']['transport_max'],
                        'cost_type'     => strtoupper($grant['budget']['type']),
                        'surcharge'     => $acceptable == $totalCost ? 0 : $totalCost - $acceptable,
                        'grant_id'      => $grant->id,
                        'title'         => $grant->config['title'],
                    ],
                ];

                if ($this->withProcessingFee) {
                    $distribution['processing_fees'] = $this->getGrantProcessingFees($grant);
                }

                return [
                    'distribution' => $distribution,
                    'overview'     => $this->setOverview($grant, $totalCost),
                ];
            }
        }


        return null;
    }

    private function distributeCosts(int|float $cost, string $type, Collection $sortedGrants): bool
    {
        foreach ($this->services as $serviceId => $serviceCost) {
            $serviceCovered = false;
            foreach ($sortedGrants as $grant) {
                $available = $grant->getAvailableBudget($this->withProcessingFee);

                if ($available >= $serviceCost) {
                    $distribution               = $this->result->getDistribution();
                    $distribution['services'][] = [
                        'id'       => $serviceId,
                        'cost'     => $serviceCost,
                        'grant_id' => $grant->id,
                        'title'    => $grant->config['title'],
                    ];
                    $this->result->setDistribution($distribution);

                    $this->result->addOverview([
                        'grant_id' => $grant->id,
                        'title'    => $grant->config['title'],
                        'before'   => $grant->budget['available'],
                        'after'    => $grant->budget['available'] - $serviceCost,
                    ]);
                    $grant->reduceAvailableBudget($serviceCost);

                    $serviceCovered = true;
                    break;
                }
            }

            if ( ! $serviceCovered) {
                return false;
            }
        }

        return true;
    }

    private function distributeProcessingFees(Collection $sortedGrants): bool
    {
        foreach ($sortedGrants as $grant) {
            $processingFee   = $grant->config['pec_fee'];
            $remainingBudget = $grant->budget['available'];

            if ($remainingBudget >= $processingFee) {
                $this->processingFeeProcessed    = true;
                $distribution                    = $this->result->getDistribution();
                $distribution['processing_fees'] = [
                    'cost'     => $processingFee,
                    'grant_id' => $grant->id,
                    'vat_id'   => $grant->event_pec_config['processing_fees_vat_id'],
                    'title'    => $grant->config['title'],
                ];
                $this->result->setDistribution($distribution);

                $this->result->addOverview([
                    'grant_id' => $grant->id,
                    'title'    => $grant->config['title'],
                    'before'   => $grant->budget['available'],
                    'after'    => $grant->budget['available'] - $processingFee,
                ]);
                $grant->reduceAvailableBudget($processingFee);

                return true;
            }
        }

        return false;
    }

    private function distributeItems(ParsedGrant $grant, array $items): array
    {
        $distribution = [];
        foreach ($items as $id => $cost) {
            $distribution[] = [
                'id'       => $id,
                'cost'     => $cost,
                'grant_id' => $grant->id,
                'title'    => $grant->config['title'],
            ];
        }

        return $distribution;
    }

    public function setEventContact(EventContact $eventContact): self
    {
        $this->eventContact = $eventContact;

        return $this;
    }

    public function setAccommodationEligibles(array $eligibles): self
    {
        $this->accommodationEligibles = $eligibles;

        return $this;
    }


    public function setTransportFeesWithTax(int|float $amount): self
    {
        $this->forTransport();
        $this->transportFeesWithTax = $amount;

        return $this;
    }

    public function setTransportFeesWhitoutTax(int|float $amount): self
    {
        $this->forTransport();
        $this->transportFeesWhitoutTax = $amount;

        return $this;
    }

    public function forTransport()
    {
        $this->forTransport = true;

        return $this;
    }

    private function transportGrant(): PecDistributionResult
    {
        $covered = $this->findTransportGrant();
        if ($covered) {
            $this->result->setCovered(PecDistributionResult::COVERED_TOTAL);
            $this->result->setDistribution($covered['distribution']);
            $this->result->addOverview($covered['overview']);
        }

        return $this->result;
    }

}
