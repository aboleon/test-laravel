<?php

namespace App\Helpers;

class EnvironmentHelper
{
    public static function isLocal(): bool
    {
        return app()->environment() === 'local';
    }
}
