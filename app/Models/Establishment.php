<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MetaFramework\Interfaces\GooglePlacesInterface;
use Illuminate\Database\Eloquent\Model;

class Establishment extends Model implements GooglePlacesInterface
{
    use HasFactory;

    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
