<?php

namespace App\Models\EventManager\Sellable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellableServiceParticipationType extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'event_sellable_service_participation';
    protected $fillable = [
        'event_sellable_service_id',
        'participation_id'
    ];
}
