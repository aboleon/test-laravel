<?php

namespace App\Models\EventManager\Accommodation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_accommodation_deposit';
    protected $guarded = [];
    protected $casts = [
      'paid_at' => 'date'
    ];

}
