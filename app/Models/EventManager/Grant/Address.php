<?php

namespace App\Models\EventManager\Grant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFramework\Interfaces\GooglePlacesInterface;

class Address extends Model implements GooglePlacesInterface
{
    use HasFactory;

    protected $table = 'event_grant_address';
    public $timestamps = false;

    protected $fillable = [
        'text_address',
        'street_number',
        'route',
        'postal_code',
        'locality',
        'administrative_area_level_2',
        'administrative_area_level_1',
        'country_code',
        'lat',
        'lon',
        'complementary'
    ];

}
