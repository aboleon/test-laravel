<?php

namespace App\Models\EventManager\Program;

use App\Models\Event;
use App\Models\PlaceRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventProgramDayRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'datetime_start' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(PlaceRoom::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(EventProgramSession::class);
    }


}
