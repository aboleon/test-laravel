<?php

namespace App\Models\EventManager;

use App\Interfaces\SageInterface;
use App\Models\Event;
use App\Models\EventManager\Accommodation\{BlockedRoom, Contingent, Deposit, Grant, RoomGroup, Service};
use App\Models\EventManager\Groups\BlockedGroupRoom;
use App\Models\Hotel;
use App\Models\Vat;
use App\Traits\SageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough, HasOne};
use MetaFramework\Accessors\VatAccessor;
use MetaFramework\Casts\PriceInteger;
use MetaFramework\Polyglote\Traits\Translation;
use MetaFramework\Traits\OnlineStatus;

/**
 * @property int $processingFeeVat
 */
class Accommodation extends Model implements SageInterface
{
    use OnlineStatus;
    use SageTrait;
    use Translation;

    protected $table = 'event_accommodation';
    protected $guarded = [];

    protected $casts
        = [
            'comission'           => PriceInteger::class,
            'comission_room'      => PriceInteger::class,
            'comission_breakfast' => PriceInteger::class,
            'processing_fee'      => PriceInteger::class,
        ];

    public array $fillables
        = [
            'title'        => [
                'label' => 'Sous-titre',
            ],
            'description'  => [
                'type'  => 'textarea',
                'label' => 'Distance / Description',
            ],
            'cancellation' => [
                'type'  => 'textarea',
                'label' => 'Annulation - Non visible en front',
            ],
        ];

    public const string SAGETAXROOM = 'roomtax';
    public const string SAGEACCOUNT = 'compte_comptable';
    public const string SAGEVAT = 'compte_tva';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'event_accommodation_id');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'event_accommodation_id');
    }

    public function roomGroups(): HasMany
    {
        return $this->hasMany(RoomGroup::class, 'event_accommodation_id');
    }

    public function contingent(): HasMany
    {
        return $this->hasMany(Contingent::class, 'event_accommodation_id');
    }

    public function blocked(): HasMany
    {
        return $this->hasMany(BlockedRoom::class, 'event_accommodation_id');
    }

    public function blockedForGroups(): HasMany
    {
        return $this->hasMany(BlockedGroupRoom::class, 'event_accommodation_id');
    }

    public function grant(): HasMany
    {
        return $this->hasMany(Grant::class, 'event_accommodation_id');
    }

    public function groups(): HasManyThrough
    {
        return $this->hasManyThrough(\App\Models\Group::class, EventGroup::class, 'event_id', 'id', 'event_id', 'group_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(\App\Models\Order\Cart\AccommodationCart::class, 'event_hotel_id');
    }

    public function vat(): BelongsTo
    {
        return $this->belongsTo(Vat::class, 'vat_id');
    }

    public function processingFeeVat(): BelongsTo
    {
        return $this->belongsTo(Vat::class, 'processing_fee_vat_id');
    }

    public function vatId(): int
    {
        return $this->vat_id ?: VatAccessor::defaultRate()->id;
    }

    public function sageFields(): array
    {
        return [
            self::SAGETAXROOM => 'Code Sage Frais dossier',
            self::SAGEACCOUNT => 'Compte comptable',
            self::SAGEVAT     => 'Compte TVA',
        ];
    }

    public function defaultSageReferenceValue(string $name = ''): string
    {
        return match ($name) {
            self::SAGETAXROOM => 'TAXRM',
            self::SAGEACCOUNT => 'ACCNT',
            self::SAGEVAT     => 'HTVAT',
            default => static::SAGEUNKNOWN,
        };
    }


    public function getSageEvent(): Event
    {
        return $this->event;
    }
}
