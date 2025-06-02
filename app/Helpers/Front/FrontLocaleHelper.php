<?php

namespace App\Helpers\Front;

use App\Accessors\Users;
use Auth;

class FrontLocaleHelper
{

    public static function getLocale(): string
    {
        $locale = app()->getLocale();
        if (null === $locale) {
            $user = Auth::user();
            if ($user) {
                $locale = Users::guessPreferredLang($user);
            } else {
                $locale = 'en';
            }
        }
        return $locale;
    }
}