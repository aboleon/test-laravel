<?php

namespace App\Helpers\Vendor\Propaganistas\LaravelPhone;

use App\Helpers\PhoneCountryHelper;

class PhoneNumber extends \Propaganistas\LaravelPhone\PhoneNumber
{
    public function getCallingCode(): string|null
    {
        $country = $this->getCountry();
        if ($country) {
            return PhoneCountryHelper::getCountryCallingCodeByCountryCode($country);
        }
        return null;
    }
}