<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class NullablePriceFloat implements CastsAttributes
{
    /**
     * @throws \Exception
     */
    public function get($model, $key, $value, $attributes): ?float
    {
        return isset($value) ? $value / 100 : null;
    }

    public function set($model, $key, $value, $attributes): ?float
    {
        return isset($value) ? $value * 100 : null;
    }
}