<?php

namespace App\Models\EventManager\Accommodation;

use App\Models\DictionnaryEntry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Room extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_accommodation_room';
    protected $guarded = [];
    private int $contingent_id;

    public function group(): BelongsTo
    {
        return $this->belongsTo(RoomGroup::class, 'room_group_id');
    }


    public function room(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'room_id');
    }


}
