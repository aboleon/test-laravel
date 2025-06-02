<?php

namespace App\Models\EventManager\Accommodation;

use App\Models\EventManager\Traits\AccommodationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use AccommodationTrait;
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_groups';
    protected $fillable = [
        'event_id',
        'group_id',
    ];



}
