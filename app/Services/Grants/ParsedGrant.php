<?php

namespace App\Services\Grants;

use Illuminate\Database\Eloquent\Model;
use MetaFramework\Accessors\VatAccessor;

/**
 * @property array $event_pec_config
 * @property int $id
 * @property array $config
 */
class ParsedGrant extends Model
{
    public $timestamps = false;
    protected $table = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected $fillable = [
        'id',
        'budget',
        'age',
        'participations',
        'domains',
        'professions',
        'locations',
        'establishments',
        'quota',
        'event_pec_config',
        'config'
    ];

    protected $casts = [
        'budget' => 'array',
        'event_pec_config' => 'array',
        'config' => 'array',
    ];

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function ofAmountType(int|float $amount, int $vat_id)
    {
        if ($this->budget['type'] == 'ht') {
            return VatAccessor::netPriceFromVatPrice($amount, $vat_id);
        }

        return $amount;
    }

    public function getAvailableBudget(bool $withProcessingFee): int|float
    {
        $available = $this->budget['available'];
        if ($withProcessingFee) {
            $available -= $this->event_pec_config['processing_fees'];
        }
        return $available;
    }

    public function reduceAvailableBudget(int|float $amount)
    {
        $budget = $this->budget;
        $budget['available'] -= $amount;
        $this->budget = $budget;
    }
}
