<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $dates = [
      'birth'
    ];

    protected $fillable = [
        'birth',
        'civ',
        'phone',

    ];

    protected $table = 'users_profile';

}
