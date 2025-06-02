<?php

namespace App\Models\EventManager\Grant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_grant_quota';

    protected $fillable = [
        'order_id',
        'grant_id',
        'type',
        'value',
        'geo_type'
    ];
}
