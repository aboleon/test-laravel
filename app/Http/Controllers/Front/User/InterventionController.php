<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\Front\FrontCache;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class InterventionController extends EventBaseController
{
    public function edit(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.intervention'));
        $user = Auth::getUser();
        $eventContact = FrontCache::getEventContact($event->id, $user->id);
        $moderators = $eventContact->programSessionModerators;
        $orators = $eventContact->programInterventionOrators;

        $items = $moderators->concat($orators);

        return view('front.user.intervention', [
            "user" => $user,
            "event" => $event,
            "items" => $items,
        ]);
    }
}
