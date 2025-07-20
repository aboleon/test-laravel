<?php

namespace App\Models\EventManager;

use App\Enum\OrderCartType;
use App\Interfaces\SageInterface;
use App\Interfaces\Stockable;
use App\Models\DictionnaryEntry;
use App\Models\Event;
use App\Models\EventManager\Sellable\Deposit;
use App\Models\EventManager\Sellable\Option;
use App\Models\EventManager\Sellable\Price;
use App\Models\EventService;
use App\Models\FrontCartLine;
use App\Models\Order\Cart\ServiceAttribution;
use App\Models\Order\Cart\ServiceCart;
use App\Models\Order\StockTemp;
use App\Models\ParticipationType;
use App\Models\Place;
use App\Models\PlaceRoom;
use App\Models\Vat;
use App\Traits\BelongsTo\BelongsToEvent;
use App\Traits\SageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasOne};
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MetaFramework\Casts\Datepicker;
use MetaFramework\Casts\PriceInteger;
use MetaFramework\Polyglote\Traits\Translation;
use MetaFramework\Traits\OnlineStatus;

/**
 * @property Collection $prices HasMany Price::class
 * @property Deposit    $deposit
 * @property null|int   $pec_eligible
 * @property int        $pec_max_pax
 * @property null|int   $stock_unlimited
 * @property int        $stock
 * @property int        $id
 */
class Sellable extends Model implements Stockable, SageInterface
{
    use HasFactory;
    use OnlineStatus;
    use Translation;
    use SageTrait;
    use SoftDeletes;
    use BelongsToEvent;

    protected $table = 'event_sellable_service';
    protected $guarded = [];

    protected $casts
        = [
            'price'           => PriceInteger::class,
            'service_date'    => Datepicker::class,
            'service_starts'  => 'datetime:H:i',
            'service_ends'    => 'datetime:H:i',
            'starts'          => 'datetime',
            'ends'            => 'datetime',
            'title'           => 'json',
            'description'     => 'json',
            'vat_description' => 'json',
        ];

    public array $fillables
        = [
            'title'           => [
                'label'    => 'Intitulé',
                'required' => true,
            ],
            'description'     => [
                'type'  => 'textarea',
                'label' => 'Description',
            ],
            'vat_description' => [
                'type'  => 'textarea',
                'label' => 'Mention TVA pour facture (si nécessaire)',
            ],
        ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'service_group');
    }

    public function groupCombined(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'service_group_combined');
    }

    public function vat(): BelongsTo
    {
        return $this->belongsTo(Vat::class, 'vat_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(PlaceRoom::class, 'room_id');
    }

    public function participations(): BelongsToMany
    {
        return $this->belongsToMany(ParticipationType::class, 'event_sellable_service_participation', 'event_sellable_service_id', 'participation_id');
    }

    public function professions(): BelongsToMany
    {
        return $this->belongsToMany(DictionnaryEntry::class, 'event_sellable_service_profession', 'event_sellable_service_id', 'profession_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'event_sellable_service_id');
    }

    public function deposit(): HasOne
    {
        return $this->hasOne(Deposit::class, 'event_sellable_service_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class, 'event_sellable_service_id');
    }

    public function scopeHasPec(Builder $query, bool $has_pec): Builder
    {
        if ($has_pec) {
            return $query->where('pec_eligible', 1);
        }

        return $query->whereNull('pec_eligible');
    }

    public function accessorDate(): string
    {
        $date = (string)$this->service_date;

        if ($this->service_starts) {
            $date .= ' '.$this->service_starts->format('H:i');
        }
        if ($this->service_end) {
            $date .= ' - '.$this->service_ends->format('H:i');
        }

        return $date;
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceCart::class, 'service_id');
    }

    public function tempBookings(): HasMany
    {
        return $this
            ->hasMany(StockTemp::class, 'shoppable_id')
            ->where('shoppable_type', Sellable::class);
    }

    public function frontBookings(): HasMany
    {
        return $this
            ->hasMany(FrontCartLine::class, 'shoppable_id')
            ->where('shoppable_type', Sellable::class)
            ->whereHas('cart', function ($query) {
                $query->whereNull('order_id');
            });
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getStockableId(): int
    {
        return $this->id;
    }

    public function getStockableType(): string
    {
        return self::class;
    }

    public function getStockableLabel(): string
    {
        return $this->title;
    }


    public function frontCartLines()
    {
        return $this->morphMany(FrontCartLine::class, 'shoppable');
    }

    public function attributions(): HasMany
    {
        return $this->hasMany(ServiceAttribution::class, 'shoppable_id')->where('shoppable_type', OrderCartType::SERVICE->value);
    }

    public function asEventService(): BelongsTo
    {
        return $this->belongsTo(EventService::class, 'service_group', 'service_id')->where('event_id', $this->event_id);
    }

    public function sageFields(): array
    {
        return [
            'code_article' => 'Référence article',
        ];
    }

    public function getSageEvent(): ?Event
    {
        return $this->event;
    }

    public function defaultSageReferenceValue(): string
    {
        return Str::upper(Str::substr(Str::slug($this->title, ''), 0, 5));
    }

    public function getSageAccountCode(): string
    {
        return $this->group->getSageReferenceValue(DictionnaryEntry::SAGEACCOUNT);
    }

    public function getSageVatAccount(): string
    {
        return $this->asEventService->getSageReferenceValue(EventService::SAGEVAT);
    }

}
