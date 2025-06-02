<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventParticipation extends Model
{
    use HasFactory;
    protected $table = 'event_participation';

    public function participation(): BelongsTo
    {
        return $this->belongsTo(ParticipationType::class);
    }
}
