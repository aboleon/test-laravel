<?php

namespace App\Http\Controllers\Front\User\Group;

use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;

class GroupCheckoutController extends EventBaseController
{
    public function index(string $locale, Event $event)
    {

        $eventContact = $this->getEventContact();
        $eventGroup = $this->getEventGroup($event);

        Seo::generator(__('front/seo.group_order_title'));


        return view('front.user.groupv2.checkout', [
            "event" => $event,
            'eventContact' => $eventContact,
            'eventGroup' => $eventGroup,
        ]);
    }
}
