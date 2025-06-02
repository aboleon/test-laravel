<?php

namespace App\Models\EventManager\Accommodation;

use App\Models\EventManager\Accommodation;
use App\Models\EventManager\Traits\AccommodationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFramework\Casts\Datepicker;

class Contingent extends Model
{
    use HasFactory;
    use AccommodationTrait;

    public $timestamps = false;
    protected $table = 'event_accommodation_contingent';
    protected $guarded = [];

    protected $casts = [
        'date' => Datepicker::class,
    ];

    public function configs(): HasMany
    {
        return $this->hasMany(ContingentConfig::class, 'contingent_id');
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class, 'event_accommodation_id');
    }

    public function roomGroup(): BelongsTo
    {
        return $this->belongsTo(RoomGroup::class, 'room_group_id');
    }

}
