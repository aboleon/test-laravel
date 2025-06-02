<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * The EventContact class, aka participant.
 */
class EventClient extends Model
{
    protected $table = 'events_clients';
    protected $fillable = [
        'user_id',
        'event_id',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(EventClient::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
