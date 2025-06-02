<?php

namespace App\Http\Controllers\Front\User;

use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;

class SelectAccountTypeController extends EventBaseController
{
    public function index(string $locale, Event $event)
    {

        Seo::generator(__('front/seo.home_title'));

        return view('front.user.select-account-type', [
            'event' => $event,
        ]);
    }
}