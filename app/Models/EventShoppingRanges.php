<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventShoppingRanges extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'event_shoprange';
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

}
