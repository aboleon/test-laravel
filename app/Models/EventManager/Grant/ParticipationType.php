<?php

namespace App\Models\EventManager\Grant;

use App\Models\EventManager\Sellable\Deposit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipationType extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_grant_participation';

    protected $fillable = [
        'participation_id',
        'pax',
        'active'
    ];

}
