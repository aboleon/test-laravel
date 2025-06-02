<?php

namespace App\Http\Controllers\Front\User\Group;

use App\Accessors\EventManager\EventGroups;
use App\DataTables\Front\EventGroupContactDataTable;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;

class GroupBuyController extends EventBaseController
{
    public function index(string $locale, Event $event)
    {

        $eventGroup = $this->getEventGroup($event);

        if (!$eventGroup) {
            return $this->redirectToDashboard($event);
        }

        $groupMembers = EventGroups::getGroupMembers($eventGroup);

        Seo::generator(__('front/seo.group_buy_title'));
        return view('front.user.groupv2.buy', [
            "event" => $event,
            'eventContact' => $this->eventContact,
            'eventGroup' => $eventGroup,
            'groupMembers' => $groupMembers
        ]);
    }
}
