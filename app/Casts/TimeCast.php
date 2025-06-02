<?php


namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TimeCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        $p = explode(':', $value);
        $hours = array_shift($p);
        $minutes = array_shift($p);
        $seconds = array_shift($p) ?? 0;
        return Carbon::createFromTime($hours, $minutes, $seconds);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }
        if ($value instanceof Carbon) {
            return $value->toTimeString();  // Returns time in 'H:i:s' format
        } elseif (is_string($value)) {
            $p = explode(':', $value);
            if (2 === count($p)) {
                return $value . ":00";
            }
        }
        return $value;
    }
}
