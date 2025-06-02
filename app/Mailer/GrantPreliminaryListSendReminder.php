<?php

namespace App\Mailer;

use App\Traits\EventCommons;
use Exception;

class GrantPreliminaryListSendReminder extends MailerAbstract
{
    use EventCommons;

    public string $eventName;
    public string $banner;
    public ?string $pecAdminEmail;
    public ?string $grantAdminEmail;
    public string $grantTitle;
    public string $deadline;

    /**
     * @throws Exception
     */
    public function setData(): self
    {
        if ( ! $this->model) {
            throw new Exception("Grant not found");
        }
        $grant = $this->model;


        $event                 = $grant->event;
        $this->banner          = $this->getBanner($event,'banner_large');
        $pec                   = $event->pec;
        $this->pecAdminEmail   = $pec->admin?->email;
        $this->grantAdminEmail = $pec->grantAdmin?->email;
        $this->grantTitle      = $grant->title;
        $this->eventName       = $event->texts->name;
        $this->deadline        = $grant?->prenotification_date?->format('d/m/Y');

        return $this;
    }

    public function email(): string|array
    {
        $ret = [];
        if ($this->pecAdminEmail) {
            $ret[] = $this->pecAdminEmail;
        }
        if ($this->grantAdminEmail) {
            $ret[] = $this->grantAdminEmail;
        }

        return $ret;
    }


    public function subject(): string
    {
        return $this->eventName.' - Rappel d\'envoi de la liste préliminaire';
    }

    public function view(): string
    {
        return 'mails.mailer.grant-preliminary-list-send-reminder';
    }

}
