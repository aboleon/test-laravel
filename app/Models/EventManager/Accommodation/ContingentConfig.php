<?php

namespace App\Models\EventManager\Accommodation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContingentConfig extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_accommodation_contingent_config';
    protected $guarded = [];

    public function roomGroup(): BelongsTo
    {
        return $this->belongsTo(RoomGroup::class, 'group_room_id');
    }
    public function rooms(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function contingent(): BelongsTo
    {
        return $this->belongsTo(Contingent::class,'contingent_id');
    }

}
