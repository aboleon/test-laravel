<?php

namespace App\Helpers;

class CsvHelper
{
    public static function csvToUniqueArray(?string $csv = null): array
    {
        return array_filter(array_unique(explode(',', (string)$csv)));
    }
}
