<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\EventManager\Sellable\EventContactSellableServiceChoosables;
use App\Accessors\Front\FrontCache;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class InvitationController extends EventBaseController
{
    public function edit(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.invitation'));
        $user = Auth::getUser();
        $eventContact = FrontCache::getEventContact($event->id, $user->id);
        $invitations = EventContactSellableServiceChoosables::getEventContactChoosables($event, $eventContact);

        $invitations = $invitations->filter(function ($sellable) {
            return $sellable->stock_unlimited == 1 or $sellable->stock > 0;
        });


        return view('front.user.invitation', [
            "invitations" => $invitations,
            "event" => $event,
            "eventContact" => $eventContact,
        ]);
    }
}
