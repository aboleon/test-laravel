<?php

namespace App\Models;

use App\DataTables\View\EventSellableServiceStockView;
use App\Enum\OrderCartType;
use App\Interfaces\CreatorInterface;
use App\Interfaces\SageInterface;
use App\Models\EventManager\{Accommodation, EventGroup, Front\FrontConfig, Grant\Grant, Grant\GrantDeposit, Program\EventProgramDayRoom, Program\EventProgramSession, Sellable, Sellable\Choosable};
use App\Models\Order\Cart\AccommodationAttribution;
use App\Models\Order\Cart\ServiceAttribution;
use App\Traits\SageTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use MetaFramework\Casts\BooleanNull;
use MetaFramework\Casts\Datepicker;
use MetaFramework\Mediaclass\Interfaces\MediaclassInterface;
use MetaFramework\Mediaclass\Traits\Mediaclass;
use MetaFramework\Traits\OnlineStatus;

/**
 * @property int        $id
 * @property mixed      $activeGrants
 * @property HasMany    $sellableService
 * @property Collection $accommodationAttributions
 * @property Collection $serviceAttributions
 * @property array|null $transport
 * @property array|null $transfert
 * @property int|null   $manage_transport_upfront
 */
class Event extends Model implements CreatorInterface, MediaclassInterface, SageInterface
{
    use HasFactory;
    use Mediaclass;
    use OnlineStatus;
    use SageTrait;
    use SoftDeletes;

    //  use SoftDeletes;

    protected $casts
        = [
            'flags'                        => 'array',
            'starts'                       => Datepicker::class,
            'ends'                         => Datepicker::class,
            'subs_ends'                    => Datepicker::class,
            'transport_tickets_limit_date' => Datepicker::class,
            'shopping_limit_date'          => Datepicker::class,
            'serialized_config'            => 'array',
            'has_abstract'                 => BooleanNull::class,
            'has_program'                  => BooleanNull::class,
            'published'                    => BooleanNull::class,
            'has_external_accommodation'   => BooleanNull::class,
            'manage_transport_upfront'     => BooleanNull::class,
            'ask_video_authorization'      => BooleanNull::class,
            'show_orators_picture'         => BooleanNull::class,
            'transport'                    => 'array',
            'transfert'                    => 'array',
        ];

    protected $guarded = [];
    public array $fillables;

    public const string SAGECODEVENT = 'code_event';
    public const string SAGECODECLIENT = 'code_client';
    public const string SAGECODESTAT = 'code_stats';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillables = (new EventTexts())->structured_fillables;
    }

    /**
     * Récupère les types de professions associés à un évènement
     */
    public function professions(): BelongsToMany
    {
        return $this->belongsToMany(DictionnaryEntry::class, 'event_profession', 'event_id', 'profession_id')->select('name', 'id', 'parent');
    }

    /**
     * Récupère les types de participation associés à un évènement
     */
    public function participations(): BelongsToMany
    {
        return $this->belongsToMany(ParticipationType::class, 'event_participation', 'event_id', 'participation_id')->select('name', 'id');
    }

    /**
     * Récupère les types de participation associés à un évènement
     */
    public function pecParticipations(): BelongsToMany
    {
        return $this->belongsToMany(ParticipationType::class, 'event_pec_participation', 'event_id', 'participation_id')->select('name', 'id');
    }

    /**
     * Récupère les types de domaines associés à un évènement, éligibles GRANT
     */
    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(DictionnaryEntry::class, 'event_domain', 'event_id', 'domain_id')->select('name', 'id');
    }

    public function pecDomains(): BelongsToMany
    {
        return $this->belongsToMany(DictionnaryEntry::class, 'event_pec_domain', 'event_id', 'domain_id')->select('name', 'id');
    }

    public function services(): BelongsToMany
    {
        return $this
            ->belongsToMany(DictionnaryEntry::class, 'event_service', 'event_id', 'service_id')
            ->withPivot(['id','max', 'unlimited', 'service_date_doesnt_count', 'fo_family_position'])
            ->wherePivot('enabled', 1);
    }

    public function eventServices(): HasMany
    {
        return $this->hasMany(EventService::class);
    }


    /**
     * Récupère les catégories d'orateurs associées à un évènement
     */
    public function orators(): BelongsToMany
    {
        return $this->belongsToMany(ParticipationType::class, 'event_orator', 'event_id', 'orator_id')->select('name', 'id');
    }

    /**
     * Récupère le PEC
     */
    public function pec(): HasOne
    {
        return $this->hasOne(EventPec::class, 'event_id');
    }

    /*
     * Récupère les contenus de présentation divers
     */
    public function texts(): HasOne
    {
        return $this->hasOne(EventTexts::class, 'event_id');
    }

    /*
     * Relation Exposant / boutique
     */
    public function shop(): HasOne
    {
        return $this->hasOne(EventShop::class, 'event_id');
    }

    public function mediaSettings(): array
    {
        return [
            '_media' =>
                [
                    'label' => 'Photos',
                ],
        ];
    }

    public function hasCreator(): bool
    {
        return (bool)$this?->created_by;
    }

    public function getCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function adminSubs(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_subs_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(EventContact::class, 'event_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(EventClient::class, 'event_id');
    }

    /**
     * Récupère les documents à fournir pour les boutiques
     */
    public function shopRanges(): HasMany
    {
        return $this->hasMany(EventShoppingRanges::class, 'event_id');
    }

    /**
     * Récupère les documents à fournir pour les boutiques
     */
    public function shopDocs(): BelongsToMany
    {
        return $this->belongsToMany(DictionnaryEntry::class, 'event_shopdocs', 'event_id', 'doc_id')->select('name', 'id');
    }

    public function sellables(): HasMany
    {
        return $this->hasMany(SellableByEvent::class, 'event_id');
    }

    public function accommodation(): HasMany
    {
        return $this->hasMany(Accommodation::class, 'event_id');
    }

    public function publishedAccommodations(): HasMany
    {
        return $this
            ->hasMany(Accommodation::class, 'event_id')
            ->whereNotNull('published');
    }


    public function grantDeposit(): HasOne
    {
        return $this->hasOne(GrantDeposit::class, 'event_id');
    }

    public function grants(): HasMany
    {
        return $this->hasMany(Grant::class);
    }

    public function activeGrants(): HasMany
    {
        return $this->hasMany(Grant::class)->where('active', 1);
    }

    public function sellableServicesWithDeposit(): HasMany
    {
        return $this->hasMany(Sellable::class, 'event_id')->whereHas('deposit');
    }


    public function publishedChoosable(): HasMany
    {
        return $this
            ->hasMany(Choosable::class, 'event_id')
            ->where('published', '=', '1')
            ->where('is_invitation', '=', '1');
    }

    public function sellableService(): HasMany
    {
        return $this->hasMany(Sellable::class, 'event_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'event_groups', 'event_id', 'group_id');
    }

    // un peu un doublon avec le précédent mais besoin de taper dans EventGroup model...
    public function eventGroups(): hasMany
    {
        return $this->hasMany(EventGroup::class, 'event_id');
    }


    public function programDays(): HasMany
    {
        return $this->hasMany(EventProgramDayRoom::class, 'event_id');
    }

    public function programSessions()
    {
        return $this->hasManyThrough(EventProgramSession::class, EventProgramDayRoom::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'event_main_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'event_type_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function rooms(): HasManyThrough
    {
        return $this->hasManyThrough(PlaceRoom::class, Place::class, 'id', 'place_id', 'place_id', 'id');
    }

    public function frontConfig(): HasOne
    {
        return $this->hasOne(FrontConfig::class, 'event_id');
    }

    public function serviceStock(): HasMany
    {
        return $this->hasMany(EventSellableServiceStockView::class, 'event_id');
    }

    public function accommodationAttributions(): HasManyThrough
    {
        return $this->hasManyThrough(
            AccommodationAttribution::class,
            Order::class,
            'event_id',
            'order_id',
            'id',
            'id',
        )->where('shoppable_type', OrderCartType::ACCOMMODATION->value);
    }


    public function serviceAttributions(): HasManyThrough
    {
        return $this->hasManyThrough(
            ServiceAttribution::class,
            Order::class,
            'event_id',
            'order_id',
            'id',
            'id',
        )->where('shoppable_type', OrderCartType::SERVICE->value);
    }

    public function mediaclassSettings(): array
    {
        return [
            'banner_large'  => [
                'width'    => 1270,
                'height'   => 140,
                'label'    => 'Bannière Large',
                'cropable' => true,
            ],
            'banner_medium' => [
                'width'    => 510,
                'height'   => 140,
                'label'    => 'Bannière Medium',
                'cropable' => true,
            ],
            'thumbnail'     => [
                'width'    => 600,
                'height'   => 380,
                'label'    => 'Image carrée',
                'cropable' => true,
            ],
        ];
    }

    public function sageFields(): array
    {
        return [
            self::SAGECODEVENT  => 'Acronyme Evènement',
            self::SAGECODECLIENT => 'Code Client',
            self::SAGECODESTAT  => 'Code Analytique',
        ];
    }

    public function getSageEvent(): self
    {
        return $this;
    }

}
