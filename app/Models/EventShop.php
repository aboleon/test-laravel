<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventShop extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'events_shops';
    protected $guarded = [];
}
