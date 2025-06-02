<?php

namespace App\Traits\BelongsTo;

use App\Models\Event;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToEvent
{
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
