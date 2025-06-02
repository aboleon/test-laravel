<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\Front\FrontCache;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;

class LoginAsController extends EventBaseController
{
    public function loginAsUser(string $locale, Event $event): RedirectResponse
    {
        FrontCache::setOldGroupManager();
        FrontCache::forgetGroupManager();

        return redirect()->route('front.event.dashboard', ['locale' => $locale, 'event' => $event->id]);
    }

    public function loginAsGroupManager(string $locale, Event $event): RedirectResponse
    {
        FrontCache::restoreOldGroupManager();

        SwitchParticipantController::disconnectAndConnectBackToGroupManager();
        return redirect()->route('front.event.group.dashboard', ['locale' => $locale, 'event' => $event->id]);
    }


}
