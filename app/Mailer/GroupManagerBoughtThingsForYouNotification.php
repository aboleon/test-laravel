<?php

namespace App\Mailer;

use App\Accessors\EventManager\EventGroups;
use App\Actions\Front\AutoConnectHelper;
use App\Mail\MailerMail;
use App\Models\EventContact;
use App\Traits\EventCommons;
use Exception;
use Illuminate\Support\Facades\Mail;
use Throwable;

class GroupManagerBoughtThingsForYouNotification extends MailerAbstract
{
    use EventCommons;

    private EventContact $eventContact;
    private EventContact $groupManagerEventContact;


    public string $eventName;
    public string $mainContactFullName;
    public string $groupManagerFullName;
    public string $groupName;
    public string $banner;
    public string $autoConnectUrl;

    /**
     * @throws Exception
     */
    public function send()
    {
        $key = $this->identifier;

        $p                          = explode('-', $key);
        $eventContactId             = $p[0];
        $groupManagerEventContactId = $p[1];


        try {
            $this->eventContact = EventContact::findOrFail($eventContactId);
        } catch (Throwable) {
            throw new Exception("EventContact not found with id ".$eventContactId);
        }

        try {
            $this->groupManagerEventContact = EventContact::findOrFail($groupManagerEventContactId);
        } catch (Throwable) {
            throw new Exception("GroupManager's EventContact not found with id ".$groupManagerEventContactId);
        }

        $eventGroup = EventGroups::getGroupByMainContact($this->groupManagerEventContact->event, $this->groupManagerEventContact->user);

        if ( ! $eventGroup) {
            throw new Exception("EventGroup not found with event_id ".$this->groupManagerEventContact->event->id." and user_id ".$this->groupManagerEventContact->user->id);
        }

        $this->banner               = $this->getBanner($this->eventContact->event,'banner_large');
        $this->eventName            = $this->eventContact->event->texts->name;
        $this->mainContactFullName  = $this->eventContact->user->first_name.' '.$this->eventContact->user->last_name;
        $this->groupManagerFullName = $this->groupManagerEventContact->user->first_name.' '.$this->groupManagerEventContact->user->last_name;
        $this->groupName            = $eventGroup->group->name;
        $this->autoConnectUrl       = AutoConnectHelper::generateAutoConnectUrlForEventContact($this->eventContact);

        return Mail::send(new MailerMail($this));
    }

    public function email(): string|array
    {
        return $this->eventContact->user->email;
    }


    public function subject(): string
    {
        return $this->eventName.' - Un achat a été fait pour vous';
    }

    public function view(): string
    {
        return 'mails.mailer.group-manager-bought-things-for-you-notification';
    }

}
