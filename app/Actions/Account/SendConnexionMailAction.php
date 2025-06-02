<?php

namespace App\Actions\Account;

use App\Actions\Ajax\AjaxAction;
use App\Mail\EventManager\Group\ConnexionMail;
use App\Models\Event;
use App\Models\EventManager\EventGroup;
use App\Models\User;
use Mail;

class SendConnexionMailAction extends AjaxAction
{

    public function sendToEventGroupMainContact(): array
    {
        return $this->handle(function (AjaxAction $a) {

            list($eventId, $groupId) = $a->checkRequestParams(['event_id', "group_id"]);

            $userId = request('user_id');
            $user = User::find($userId);
            if (null === $user) {
                $this->responseError("Veuillez choisir un contact");
                return;
            }

            $event = Event::find($eventId);
            if (null === $event) {
                throw new \Exception('Event not found');
            }

            $group = $event->groups()->find($groupId);
            if (null === $group) {
                throw new \Exception('Group not found');
            }

            $eventGroup = EventGroup::where('event_id', $eventId)
                ->where('group_id', $groupId)
                ->first();
            if (null === $eventGroup) {
                throw new \Exception('EventGroup not found');
            }
            if ($userId && (int)$userId !== (int)$eventGroup->main_contact_id) {
                $this->responseError("Le contact choisi n'est pas le contact principal du groupe");
                return;
            }


            Mail::to($user->email)->send(new ConnexionMail($user, $event, $group));
            $a->responseSuccess('Mail envoyé à ' . $user->email);
        });
    }
}