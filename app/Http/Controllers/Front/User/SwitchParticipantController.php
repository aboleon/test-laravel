<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\Front\FrontCache;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SwitchParticipantController extends EventBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function disconnectAndBackToGroupMembers(string $locale, Event $event)
    {
        self::disconnectAndConnectBackToGroupManager();

        return redirect()->route('front.event.group.members', [
            'locale' => $locale,
            'event'  => $event,
        ]);
    }


    public function disconnectAndBackToGroupCart(string $locale, Event $event)
    {
        self::disconnectAndConnectBackToGroupManager();

        return redirect()->route('front.event.group.checkout', [
            'locale' => $locale,
            'event'  => $event,
        ]);
    }

    public function disconnectAndBackToGroupBuy(string $locale, Event $event)
    {
        self::disconnectAndConnectBackToGroupManager();

        return redirect()->route('front.event.group.buy', [
            'locale' => $locale,
            'event'  => $event,
        ]);
    }


    public function connectAsGroupMember(string $locale, Event $event, Group $group, User $user, string $routeType)//: RedirectResponse
    {
        $eventGroup = $event
            ->eventGroups()
            ->where('group_id', $group->id)
            ->where('main_contact_id', $this->eventContact->user_id)
            ->first();


        if ( ! $eventGroup) {
            return redirect()->back()->with("general.error", __('errors.event_contact_is_not_group_manager'));
        }

        if ( ! $eventGroup->eventGroupContacts()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with("general.error", __('errors.event_contact_not_linked_to_group'));
        }


        FrontCache::setGroupManager([
            'intent' => $routeType,
        ]);
        FrontCache::setEventContact($event->id, $user->id, impersonated: true);
      //  Auth::loginUsingId(FrontCache::getEventContact()->user_id);


        /*
                d(FrontCache::getEventContact(), 'NEW EC');
                d(FrontCache::getGroupManager(),'GM');


*/
        return redirect()->to(
            $this->getUrl($routeType, $locale, $event),
        );

    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public static function disconnectAndConnectBackToGroupManager()
    {
        $groupManager = FrontCache::getGroupManager();
        if ($groupManager) {
            FrontCache::setEventContact(FrontCache::getEvent()?->id, $groupManager->user_id);
            session()->forget('front_group_manager_params');
        }
    }

    /**
     * @param  string  $routeType
     * @param  string  $locale
     * @param  Event   $event
     *
     * @return string
     */
    public function getUrl(string $routeType, string $locale, Event $event): string
    {
        return match ($routeType) {
            'general-info' => route('front.event.account.edit', [
                'locale' => $locale,
                'event'  => $event->id,
            ]),
            'services' => route('front.event.service_and_registration.edit', [
                'locale' => $locale,
                'event'  => $event->id,
            ]),
            'accommodations' => route('front.event.accommodation.edit', [
                'locale' => $locale,
                'event'  => $event->id,
            ]),
            default => route('front.event.dashboard', [
                'locale' => $locale,
                'event'  => $event->id,
            ])
        };
    }
}
