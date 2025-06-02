<?php

namespace App\Models\EventManager\Groups;

use App\Models\EventManager\Accommodation\RoomGroup;
use App\Models\EventManager\Traits\AccommodationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFramework\Casts\Datepicker;
use MetaFramework\Casts\ForceInteger;

class BlockedGroupRoom extends Model
{
    use AccommodationTrait;
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_accommodation_blocked_group_room';
    protected $guarded = [];

    protected $casts = [
        'date' => Datepicker::class,
        'total' => ForceInteger::class,
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(RoomGroup::class, 'group_room_id');
    }

}
