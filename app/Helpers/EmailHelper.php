<?php

namespace App\Helpers;

class EmailHelper
{

    public static function emailValid(string $email): bool
    {
        return (false !== filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}