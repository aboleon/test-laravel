<?php

namespace App\Models;

use App\Casts\EmptyToNull;
use App\DataTables\View\EventGrantStatView;
use App\Enum\ClientType;
use App\Enum\OrderCartType;
use App\Enum\OrderClientType;
use App\Enum\OrderType;
use App\Models\EventManager\EventGroup;
use App\Models\EventManager\EventGroup\EventGroupContact;
use App\Models\EventManager\Program\EventProgramInterventionOrator;
use App\Models\EventManager\Program\EventProgramSessionModerator;
use App\Models\EventManager\Sellable\Choosable;
use App\Models\EventManager\Transport\EventTransport;
use App\Models\Order\Attribution;
use App\Models\Order\Cart\AccommodationAttribution;
use App\Models\Order\Cart\ServiceAttribution;
use App\Models\Order\EventDeposit;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MetaFramework\Casts\BooleanNull;


/**
 * The EventContact class, aka participant.
 *
 * @property bool                       $is_pec_eligible
 * @property ?int                       $pec_fees_apply
 * @property HasMany                    $pecDistributions
 * @property ?int                       $pec_enabled
 * @property string|null                $registration_type
 * @property AccountProfile             $profile
 * @property null|int                   $order_cancellation
 * @property int                        $user_id
 * @property int                        $event_id
 * @property null|ParticipationType     $participationType // BelongsTo
 * @property Account                    $account
 * @property Event                      $event
 * @property HasMany|Attribution        $attributions
 * @property HasMany|ServiceAttribution $serviceAttributions
 * @property mixed                      $accommodationAttributions
 * @property EventDeposit               $grantDeposit      // HasOne
 * @property bool|null                  $grant_deposit_not_needed
 * @property EventGrantStatView         $grantStats // HasMany
 */
class EventContact extends Model
{
    use HasFactory;

    protected $table = 'events_contacts';
    protected $fillable
        = [
            'user_id',
            'event_id',
            'participation_type_id',
            'registration_type',
            'is_attending',
            'order_cancellation',
            'fo_group_manager_request_sent',
            'comment',
            'is_pec_eligible',
            'pec_enabled',
            'pec_fees_apply',
            'grant_deposit_not_needed',
        ];
    protected $casts
        = [
            "participation_type_id"         => EmptyToNull::class,
            "is_attending"                  => BooleanNull::class,
            "order_cancellation"            => BooleanNull::class,
            "fo_group_manager_request_sent" => BooleanNull::class,
        ];


    public function contacts(): HasMany
    {
        return $this->hasMany(EventContact::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(AccountProfile::class, 'user_id', 'user_id');
    }

    public function address(): HasMany
    {
        return $this->hasMany(AccountAddress::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id', 'user_id')->where(
            [
                'client_type' => OrderClientType::CONTACT->value,
                'type'  => OrderType::ORDER->value,
            ],
        );
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function participationType(): BelongsTo
    {
        return $this->belongsTo(ParticipationType::class);
    }

    public function transport(): HasOne
    {
        return $this->hasOne(EventTransport::class, 'events_contacts_id');
    }

    public function programInterventionOrators(): hasMany
    {
        return $this->hasMany(EventProgramInterventionOrator::class, 'events_contacts_id');
    }

    public function programSessionModerators(): hasMany
    {
        return $this->hasMany(EventProgramSessionModerator::class, 'events_contacts_id');
    }

    public function choosables(): BelongsToMany
    {
        return $this
            ->belongsToMany(Choosable::class, 'event_contact_sellable_service_choosables')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function pecDistributions(): HasMany
    {
        return $this->hasMany(PecDistribution::class, 'event_contact_id');
    }

    public function attributions(): HasMany
    {
        return $this->hasMany(Attribution::class, 'event_contact_id');
    }

    public function serviceAttributions(): HasMany
    {
        return $this->hasMany(ServiceAttribution::class, 'event_contact_id')->where('shoppable_type', OrderCartType::SERVICE->value)->with('order');
    }

    public function accommodationAttributions(): EloquentCollection
    {
        return $this
            ->hasMany(AccommodationAttribution::class, 'event_contact_id')
            ->where('shoppable_type', OrderCartType::ACCOMMODATION->value)
            ->with(['order.account', 'order.group', 'shoppable.group.hotel.hotel', 'shoppable.room'])->get();
    }

    public function grantDeposit(): HasOne
    {
        return $this->hasOne(EventDeposit::class, 'event_contact_id');
    }

    public function grantStats(): HasMany
    {
        return $this->hasMany(EventGrantStatView::class, 'event_contact_id');
    }

    public function eventGroups(): HasManyThrough
    {
        return $this->hasManyThrough(
            EventGroup::class,
            EventGroupContact::class,
            'user_id',
            'id',
            'user_id',
            'event_group_id'
        );
    }

    public function accommodationAttributionsRelation(): HasMany
    {
        return $this->hasMany(AccommodationAttribution::class, 'event_contact_id')->where('shoppable_type', OrderCartType::ACCOMMODATION->value);
    }
}
