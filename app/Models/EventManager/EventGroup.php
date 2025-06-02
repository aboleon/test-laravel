<?php

namespace App\Models\EventManager;

use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\EventGroup\EventGroupContact;
use App\Models\EventManager\Groups\BlockedGroupRoom;
use App\Models\Group;
use App\Models\Order;
use App\Models\ParticipationType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class EventGroup extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'event_groups';

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function mainContact(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_contact_id');
    }

    public function participationType(): BelongsTo
    {
        return $this->belongsTo(ParticipationType::class, 'main_contact_participation_type_id');
    }

    public function blockedRooms(): HasMany
    {
        return $this->hasMany(BlockedGroupRoom::class, 'event_group_id');
    }

    public function eventGroupContacts(): HasMany
    {
        return $this->hasMany(EventGroupContact::class, 'event_group_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id', 'group_id')->where(
            [
                'client_type' => 'group',
                'event_id' => $this->event_id
            ]
        );
    }

    public function eventContacts(): HasManyThrough
    {
        return $this->hasManyThrough(EventContact::class,
            EventGroupContact::class,
            'event_group_id',
            'user_id',
            'id',
            'user_id'
        )->whereHas('event', function ($query) {
            $query->where('id', $this->event_id);
        });
    }
}
