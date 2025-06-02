<?php

namespace App\Helpers\Front;

class PriceHelper
{
    public static function frontPriceWithoutDecimal(float $price): string
    {
        return \MetaFramework\Accessors\Prices::readableFormat($price, '€', ',', '', 0, 'pc');
    }

    public static function frontPriceWithDecimal(float $price): string
    {
        return \MetaFramework\Accessors\Prices::readableFormat($price, '€', ',', '', 2, 'pc');
    }
}