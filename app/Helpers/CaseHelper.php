<?php

namespace App\Helpers;


class CaseHelper
{


    public static function toPascal(string $string): string
    {
        $string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
        $string = ucwords($string);
        return str_replace(' ', '', $string);
    }

    public static function dashToCamel(string $string): string
    {
        $words = explode('-', $string);
        $words = array_map(function ($word, $key) {
            return $key === 0 ? $word : ucfirst($word);
        }, $words, array_keys($words));
        return implode('', $words);
    }
}