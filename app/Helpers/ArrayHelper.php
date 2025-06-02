<?php

namespace App\Helpers;

class ArrayHelper
{
    public static function toString($array, $pairSeparator = ';', $keyValueSeparator = '='): string
    {
        $stringParts = [];
        foreach ($array as $key => $value) {
            $stringParts[] = $key . $keyValueSeparator . $value;
        }
        return implode($pairSeparator, $stringParts);
    }
}