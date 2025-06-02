<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaceRoomSetup extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'place_rooms_setup';
    protected $fillable = [
        'name',
        'capacity',
        'description'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(PlaceRoom::class, 'place_room_id');
    }

}
