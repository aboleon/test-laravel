<?php

namespace App\Services\Filters\Interfaces;

use App\Services\Filters\FilterParser;

interface FilterProviderInterface
{
    public static function getFilters(FilterParser $distributor): array;
}
