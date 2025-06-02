<?php

namespace App\Accessors\EventManager\Grant;

use App\Services\Grants\ParsedGrant;
use App\Services\Pec\PecType;
use App\Traits\Models\EventGrantModelTrait;
use Illuminate\Support\Facades\DB;

class GrantAccessor
{
    use EventGrantModelTrait;

    public function parsedGrandData(ParsedGrant $grant): array
    {
        return [
            'id'      => $grant->id,
            'title'   => $grant['config']['title'],
            'deposit' => $grant['config']['deposit_fee'],
            'vat_id'  => $grant['event_pec_config']['waiver_fees_vat_id'],
        ];
    }

    public function getUsedAmount(): float|int
    {
        return ($this->eventGrant->pecDistributions()->where('type','!=', PecType::TRANSPORT->value)->sum(DB::raw('total_net + total_vat'))
            + $this->eventGrant->pecDistributions()->where('type','=', PecType::TRANSPORT->value)->sum('unit_price'))
            / 100;
    }

    public function totalAmount(): float|int
    {
        return $this->eventGrant->amount;
    }

    public function availableAmount(): float|int
    {
        return $this->totalAmount() - $this->getUsedAmount();
    }

    public function type(): string
    {
        return $this->eventGrant->amount_type;
    }
}
