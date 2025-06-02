<?php

namespace App\Http\Controllers\Front;


use App\Accessors\EventManager\EventGroups;
use App\Accessors\Front\FrontCache;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\EventGroup;
use App\Traits\Models\EventContactModelTrait;
use Exception;

class EventBaseController
{
    use EventContactModelTrait;

    public function __construct()
    {
        $this->setEventContact(FrontCache::getEventContactModel());
    }

    /**
     * @throws Exception
     */
    protected function getEventContact(): EventContact
    {
        $this->throwException()->validateModelProperty('eventContact', __('errors.user_unknown'));

        return $this->eventContact;
    }

    protected function getEventGroup(Event $event): ?EventGroup
    {
        if (!$this->eventContact) {
            return null;
        }
        return EventGroups::getGroupByMainContact($event, $this->eventContact->user);
    }

    protected function redirectToDashboard(Event $event)
    {
        $url = route('front.event.dashboard', ['locale' => app()->getLocale(), 'event' => $event->id]);

        return redirect()->to($url);
    }
}
