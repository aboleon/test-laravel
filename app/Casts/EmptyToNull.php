<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EmptyToNull implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): ?int
    {
        if ($value === '0' || $value === '' || $value === null) {
            return null;
        }
        return (int)$value;
    }

    public function set($model, $key, $value, $attributes): ?int
    {
        if ($value === '0' || $value === '' || $value === null) {
            return null;
        }

        return (int)$value;
    }
}