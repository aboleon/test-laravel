<?php

namespace App\Helpers\Log;

/**
 * The default Log facade by Laravel was showing me errors that were unrelated to my issues, so I created this helper to log my own messages only.
 */
class LogHelper
{
    public static function debug(string $message)
    {
        $file = __DIR__ . "/../../../storage/logs/debug.log";
        file_put_contents($file, $message . "\n", FILE_APPEND);
    }
}