<?php

namespace App\Mailer;

use App\Mail\MailerMail;
use App\Models\EventContact;
use App\Traits\EventCommons;
use Exception;
use Illuminate\Support\Facades\Mail;
use Throwable;

class GrantDepositAcceptedNotification extends MailerAbstract
{
    use EventCommons;

    private EventContact $eventContact;
    public string $eventName;
    public string $mainContactFullName;
    public string $banner;


    /**
     * @throws Exception
     */
    public function setData(): self
    {
        if ( !$this->model instanceof EventContact) {
            throw new Exception("EventContact not found");
        }

        $this->eventContact = $this->model;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function send()
    {

        $this->banner              = $this->getBanner($this->eventContact->event,'banner_large');
        $this->eventName           = $this->eventContact->event->texts->name;
        $this->mainContactFullName = $this->eventContact->user->first_name.' '.$this->eventContact->user->last_name;

        return Mail::send(new MailerMail($this));
    }

    public function email(): string|array
    {
        return $this->eventContact->user->email;
    }


    public function subject(): string
    {
        return $this->eventName.' - Votre prise en charge est activée';
    }

    public function view(): string
    {
        return 'mails.mailer.grant-deposit-accepted-notification';
    }

}
