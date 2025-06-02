<?php

namespace App\Http\Controllers\Front\User\Group;

use App\DataTables\Front\EventGroupContactDataTable;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;

class GroupMembersController extends EventBaseController
{
    public function index(string $locale, Event $event)
    {

        $eventContact = $this->getEventContact();
        $eventGroup = $this->getEventGroup($event);

        if (!$eventGroup) {
            return $this->redirectToDashboard($event);
        }


        Seo::generator(__('front/seo.group_dashboard_title'));
        $dataTable = new EventGroupContactDataTable($event, $eventGroup);
        return $dataTable->render('front.user.groupv2.members', [
            "event" => $event,
            'eventContact' => $eventContact,
            'eventGroup' => $eventGroup,
            "dataTable" => $dataTable,
        ]);
    }
}
