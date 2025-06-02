<?php

namespace App\Mailer;

use App\Accessors\Accounts;
use App\Accessors\EventManager\TransportAccessor;
use App\Mail\MailerMail;
use App\Traits\EventCommons;
use App\Traits\Models\AccountModelTrait;
use App\Traits\Models\EventTransportModelTrait;
use Exception;
use Illuminate\Support\Facades\Mail;

class TransportManagement extends MailerAbstract
{
    use EventCommons;
    use EventTransportModelTrait;
    use AccountModelTrait;

    public array $data = [];


    public function send(): self
    {
        if (Mail::send(new MailerMail($this))) {
            $this->eventTransport->management_mail = now();
            $this->eventTransport->save();
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function setData(): void
    {
        $this
            ->setEventTransport($this->identifier)
            ->throwException()
            ->validateModelProperty('eventTransport');

        $this->setAccount($this->eventTransport->eventContact->account);
        $this->account->load('profile');


        $this->data = [
            'transport'         => $this->eventTransport,
            'transportAccessor' => (new TransportAccessor())->setEventTransport($this->eventTransport),
            'banner'            => $this->getBanner($this->eventTransport->eventContact->event,'banner_large'),
        ];
    }

    public function addressee(): string
    {
        return $this->account->names();
    }

    public function accountLanguage(): string
    {
        return (new Accounts($this->account))->getLocale();
    }

    public function email(): string|array
    {
        return $this->account->email;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function subject(): string
    {
        return __('front/transport.management_change');
    }

    public function view(): string
    {
        return 'mails.mailer.transport_management_change';
    }

}
