<?php

namespace App\Services\Grants;

use App\Accessors\EventManager\Grant\GrantAccessor;
use App\Accessors\EventManager\SellableAccessor;
use App\Models\Event;
use App\Models\PecDistribution;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MetaFramework\Traits\Responses;

class GrantParser
{
    use Responses;

    private null|EloquentCollection|Collection $activeGrants = null;
    private ?Collection $usage = null;
    private ?Collection $openGrants = null;
    private ?Collection $eligibles = null;

    public readonly int $min_sellable_price;

    public function __construct(public Event $event)
    {
        $this->min_sellable_price = SellableAccessor::cheapestForEvent($this->event);
    }

    /**
     * Récupère les grants actifs de l'évènement
     *
     * @return EloquentCollection|Collection
     */
    public function fetchActiveGrants(): EloquentCollection|Collection
    {
        if ( ! $this->event->pec?->is_active) {
            $this->responseError("La PEC n'est pas activée sur cet évènement.");

            return collect();
        }

        if ($this->activeGrants !== null) {
            return $this->activeGrants;
        }

        $this->activeGrants = $this->event->activeGrants;

        return $this->activeGrants;
    }

    /**
     * Récupère les grants dispos avec leurs critères et budget
     *
     * @return ?Collection
     */
    public function fetchAvailableGrants(): ?Collection
    {
        $this->parseEligibles();

        return $this->eligibles;
    }

    /**
     * Récupère l'usage du budget et le nombre d'attribution parmis les grants actifs
     */
    private function calculateUsage(): void
    {
        if ($this->usage !== null) {
            return;
        }

        $this->fetchActiveGrants();

        if ($this->hasErrors()) {
            $this->usage = collect();

            return;
        }

        $this->activeGrants->load('pecDistributions');

        $accounts = $this->getPaxCount();

        $this->usage = $this->activeGrants->mapWithKeys(function ($grant) use ($accounts) {
            $grantAccessor = (new GrantAccessor())->setEventGrant($grant->load('pecDistributions'));

            return [
                $grant->id => [
                    'spent'        => $grantAccessor->getUsedAmount(),
                    'orders'       => $grant->pecDistributions->pluck('order_id')->unique()->toArray(),
                    'pax_financed' => $accounts[$grant->id] ?? 0,
                ],
            ];
        });
    }

    private function getPaxCount(): array
    {
        return PecDistribution::query()
            ->whereIn('grant_id', $this->activeGrants->pluck('id'))
            ->selectRaw('count(distinct `grant_id`) as count, grant_id')
            ->groupBy('grant_id')
            ->pluck('count', 'grant_id')
            ->toArray();
    }

    /**
     * Récupère les parmis les grants actifs, ceux qui ont du budget restant et n'ont pas atteint la limite d'attribution
     */
    private function determineOpenGrants(): void
    {
        if ($this->openGrants !== null) {
            return;
        }

        $this->calculateUsage();

        if ($this->hasErrors() or $this->usage->isEmpty()) {
            $this->openGrants = collect();

            return;
        }


        $this->openGrants = $this->activeGrants->filter(function ($item) {
            $id    = $item['id'];
            $usage = $this->usage[$id];

            $paxMaxCondition = ! $item['pax_max'] || ($item['pax_max'] > 0 && $usage['pax_financed'] < $item['pax_max']);

            return $item['amount'] > $usage['spent'] && $paxMaxCondition && $item['amount'] > $this->min_sellable_price;
        });
    }

    /**
     * Récupère les critères d'éligibilité propre aux grants
     * Source : les open grants (ceux qui sont actifs, ont du budget et n'ont pas atteint la limite d'attribution)
     */
    private function parseEligibles(): void
    {
        if ($this->eligibles !== null) {
            return;
        }

        $this->determineOpenGrants();

        if ($this->hasErrors() or $this->openGrants->isEmpty()) {
            $this->eligibles = collect();

            return;
        }

        $this->openGrants->load('domains', 'participationTypes', 'professions', 'locations', 'establishments', 'quota');

        $this->eligibles = $this->openGrants->map(function ($grant) {
            return (new ParsedGrant([
                'id'               => $grant->id,
                'config'           => [
                    'title'       => $grant->title,
                    'pec_fee'     => $grant->pec_fee ?: $this->event->pec->processing_fees,
                    'deposit_fee' => $grant->deposit_fee ?: $this->event->pec->waiver_fees,
                ],
                'budget'           => [
                    'type'                   => $grant->amount_type,
                    'initial'                => $grant->amount,
                    'spent'                  => $this->usage[$grant->id]['spent'],
                    'available'              => $grant->amount - $this->usage[$grant->id]['spent'],
                    'pax_financed'           => $this->usage[$grant->id]['pax_financed'],
                    'pax_max'                => $grant->pax_max,
                    'transport_max'          => $grant->refund_transport_amount,
                    'allow_transport_refund' => $grant->refund_transport,
                ],
                'event_pec_config' => Arr::except($this->event->pec->toArray(), ['id', 'event_id', 'is_active']),
                'age'              => ['min' => $grant->age_eligible_min, 'max' => $grant->age_eligible_max],
                'participations'   => $this->getEligibleItems($grant->participationTypes, 'participation_id'),
                'domains'          => $this->getEligibleItems($grant->domains, 'domain_id'),
                'professions'      => $this->getEligibleItems($grant->professions, 'profession_id'),
                'locations'        => $grant->locations->toArray(),
                'establishments'   => $this->getEligibleItems($grant->establishments, 'establishment_id'),
                'quota'            => $grant->quota,

            ]));
        });
    }

    /**
     * Utilitaire sur parseEligibles()
     *
     * @param          $items
     * @param  string  $key
     *
     * @return array
     */
    private function getEligibleItems($items, string $key): array
    {
        $active = $items->filter(fn($item) => ! is_null($item->active));

        if ($active->isEmpty()) {
            $active = $items;
        }

        return $active->pluck('pax', $key)->toArray();
    }

    /**
     * Utilitaire sur usage()
     *
     * @param $usages
     *
     * @return int
     */
    private function getUsedAmount($usages): int
    {
        return $usages->sum('amount');
    }

}
