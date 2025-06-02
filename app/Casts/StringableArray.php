<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use DateTime;

class StringableArray implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): array
    {
        return explode(',', $value);
    }

    public function set($model, $key, $value, $attributes): ?string
    {
        if ($value) {
            return implode(',', (array)$value);
        }
        return null;
    }
}
