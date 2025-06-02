<?php

namespace App\Models\EventManager\Grant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'event_grant_contact';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'fonction',
        'service',
    ];

    protected $casts = [
       // 'phone' => E164PhoneNumberCast::class.':FR',
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
