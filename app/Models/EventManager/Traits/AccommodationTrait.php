<?php

namespace App\Models\EventManager\Traits;

use App\Models\EventManager\Accommodation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait AccommodationTrait
{
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class, 'event_accommodation_id');
    }
}
