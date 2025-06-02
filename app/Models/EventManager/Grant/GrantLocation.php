<?php

namespace App\Models\EventManager\Grant;

use App\Abstract\GrantLocationAbstract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MetaFramework\Casts\NullablePriceInteger;

class GrantLocation extends GrantLocationAbstract
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'event_grant_location';

    protected $guarded = [];

    protected $casts = [
        'amount' => NullablePriceInteger::class
    ];

    public function fields(): array
    {
        return [
            'amount' => 'number',
            'pax' => 'number'
        ];
    }
}
