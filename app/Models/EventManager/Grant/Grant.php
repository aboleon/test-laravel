<?php

namespace App\Models\EventManager\Grant;

use App\Models\Event;
use App\Models\PecDistribution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use MetaFramework\Casts\BooleanNull;
use MetaFramework\Casts\PriceInteger;
use MetaFramework\Polyglote\Traits\Translation;
use MetaFramework\Traits\OnlineStatus;

class Grant extends Model
{
    use OnlineStatus;
    use Translation;
    use SoftDeletes;

    protected $table = 'event_grant';

    protected $casts = [
        'amount' => PriceInteger::class,
        'pec_fee' => PriceInteger::class,
        'deposit_fee' => PriceInteger::class,
        'refund_transport_amount' => PriceInteger::class,
        'prenotification_date' => 'date',
        'manage_transport_upfront' => BooleanNull::class,
        'manage_transfert_upfront' => BooleanNull::class,
        'refund_transport' => BooleanNull::class,
        'active' => BooleanNull::class,
        'comment' => 'array',
        'title' => 'array',
        'refund_transport_text' => 'array'
    ];

    protected $fillable = [
        'active',
        'title',
        'amount',
        'amount_type',
        'pax_min',
        'pax_max',
        'pax_avg',
        'pec_fee',
        'deposit_fee',
        'manage_transport_upfront',
        'manage_transfert_upfront',
        'refund_transport',
        'refund_transport_amount',
        'age_eligible_min',
        'age_eligible_max',
        'refund_transport_text',
        'comment',
        'prenotification_date'
    ];

    public array $fillables = [

        'title' => [
            'label' => 'Intitulé',
            'required' => true,
        ],
        'comment' => [
            'label' => 'Commentaire',
            'type' => 'textarea',
        ],
        'refund_transport_text' => [
            'label' => 'Informations remboursement pour le site',
            'type' => 'textarea',
            'required' => true,
        ],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }

    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class, 'grant_id');
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'grant_id');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'grant_id');
    }

    public function participationTypes(): HasMany
    {
        return $this->hasMany(ParticipationType::class, 'grant_id');
    }

    public function professions(): HasMany
    {
        return $this->hasMany(Profession::class, 'grant_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(GrantLocation::class, 'grant_id');
    }

    public function establishments(): HasMany
    {
        return $this->hasMany(Establishment::class, 'grant_id');
    }

    public function establishmentsWithCountry(): HasMany
    {
        return $this->establishments()->with(['establishment.country']);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function orderCartGrantDeposits(): HasMany
    {
        return $this->hasMany(\App\Models\Order\Cart\GrantDepositCart::class, 'event_grant_id');
    }
    public function quota(): HasMany
    {
        return $this->hasMany(Quota::class, 'grant_id');
    }

    public function pecDistributions(): HasMany
    {
        return $this->hasMany(PecDistribution::class, 'grant_id');
    }

}
