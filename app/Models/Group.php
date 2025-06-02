<?php

namespace App\Models;

use App\Interfaces\CreatorInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

/**
 * @property GroupAddress $address
 */
class Group extends Model implements CreatorInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'phone' => E164PhoneNumberCast::class . ':country_code',
    ];


    public function address(): HasMany
    {
        return $this->hasMany(GroupAddress::class, 'group_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(GroupContact::class, 'group_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withoutGlobalScope('active');
    }

    public function hasCreator(): bool
    {
        return (bool)$this?->created_by;
    }

    public function getCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_groups', 'group_id', 'event_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_contacts', 'group_id', 'user_id');
    }

    public function eventGroups(): HasMany
    {
        return $this->hasMany(EventGroup::class, 'group_id');
    }

    public function names(): string
    {
        return $this->name;
    }
}
