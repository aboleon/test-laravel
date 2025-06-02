<?php

namespace App\Http\Controllers\Front\User\Group;

use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use App\Traits\Front\Groups;

class GroupDashboardController extends EventBaseController
{
    use Groups;

    public function index(string $locale, Event $event)
    {
        $eventGroup = $this->getEventGroup($event);


        $this->initGroupAccessor();
        $this->groupAccessor->setEvent($event);


        Seo::generator(__('front/seo.group_dashboard_title'));

        return view('front.user.groupv2.dashboard', [
            'hasAttributions' => $this->groupAccessor->hasGroupOrders()
        ]);
    }
}
