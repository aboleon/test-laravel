<?php

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use DateTime;
use Throwable;

class SellablePriceDateTime implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
        } catch (Throwable) {
            return null;
        }
    }

    public function set($model, $key, $value, $attributes)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }
}
