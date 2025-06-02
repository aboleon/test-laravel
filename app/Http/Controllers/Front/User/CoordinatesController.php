<?php

namespace App\Http\Controllers\Front\User;

use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class CoordinatesController extends EventBaseController
{
    public function edit(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.address_title'));
        $user = Auth::getUser();
        return view('front.user.coordinates', [
            "user" => $user,
            "event" => $event,
        ]);
    }
}