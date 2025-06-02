<?php

namespace App\Services\Validators;

use MetaFramework\Interfaces\GooglePlacesInterface;

class AddressValidator
{

    /**
     * @param  ?GooglePlacesInterface  $address
     *
     * @return bool
     */

    public function __construct(private ?GooglePlacesInterface $address)
    {

    }

    private array $required
        = [
            //'street_number',
            //'route',
            'locality',
            'postal_code',
            'country_code',
            'text_address',
            'lat',
            'lon',
        ];

    public  function isValid(): bool
    {
        return collect($this->required)->every(fn($field) => !empty($this->address->$field));
    }

}
