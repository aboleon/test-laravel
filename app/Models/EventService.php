<?php

namespace App\Models;

use App\Interfaces\SageInterface;
use App\Traits\BelongsTo\BelongsToEvent;
use App\Traits\SageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventService extends Model implements SageInterface
{
    use SageTrait;
    use BelongsToEvent;

    protected $table = 'event_service';
    protected $fillable = [
        'event_id',
        'service_id',
        'max',
        'unlimited',
        'service_date_doesnt_count',
        'fo_family_position',
    ];

    protected $casts = [
        'max' => 'integer',
        'unlimited' => 'boolean',
        'service_date_doesnt_count' => 'boolean',
        'fo_family_position' => 'integer',
    ];

    public const string SAGEVAT = 'compte_tva';

    public function serviceFamily(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'service_id');
    }


    public function sageFields(): array
    {
        return [
            self::SAGEVAT => 'Compte TVA'
        ];
    }

    public function defaultSageReferenceValue(): string
    {
        return 'FMVAT';
    }

    public function getSageEvent(): Event
    {
        return $this->event;
    }
}
