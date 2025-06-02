<?php

namespace App\Models\EventManager\Accommodation;

use App\Models\EventManager\Traits\AccommodationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFramework\Casts\Datepicker;
use MetaFramework\Casts\ForceInteger;

class BlockedRoom extends Model
{
    use HasFactory;
    use AccommodationTrait;

    public $timestamps = false;
    protected $table = 'event_accommodation_blocked_room';
    protected $guarded = [];

    protected $casts = [
        'date' => Datepicker::class,
        'total' => ForceInteger::class,
        'grant' => ForceInteger::class,
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(RoomGroup::class, 'group_room_id');
    }

}
