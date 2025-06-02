<?php

namespace App\Models\EventManager\EventGroup;

use App\Models\EventManager\EventGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $event_group_id
 * @property int $user_id
 * @property User $user
 */
class EventGroupContact extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'event_group_contacts';

    public function eventGroup(): BelongsTo
    {
        return $this->belongsTo(EventGroup::class, 'event_group_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
