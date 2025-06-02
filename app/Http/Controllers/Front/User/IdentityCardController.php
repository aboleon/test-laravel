<?php

namespace App\Http\Controllers\Front\User;

use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class IdentityCardController extends EventBaseController
{
    public function edit(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.identity_card_title'));
        $user = Auth::getUser();
        return view('front.user.identity-card', [
            "user" => $user,
            "event" => $event,
        ]);
    }
}