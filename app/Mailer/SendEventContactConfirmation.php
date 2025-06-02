<?php

namespace App\Mailer;


use App\Accessors\Accounts;
use App\Mail\MailerMail;
use App\Models\EventContact;
use App\Traits\EventCommons;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class SendEventContactConfirmation extends MailerAbstract
{
    use EventCommons;

    public array $data = [];

    private ?EventContact $eventContact;

    private ?Accounts $accountAccessor;


    public function send()
    {
        return Mail::send(new MailerMail($this));
    }

    public function setData(): void
    {
        $this->eventContact = EventContact::where('uuid', '=', $this->identifier)->with(['account', 'event.texts'])->first();

        if (!$this->eventContact) {
            abort(404, "Contact not found with identifier " . $this->identifier);
        }

        $this->accountAccessor = new Accounts($this->eventContact->account);
        App::setLocale($this->accountAccessor->getLocale());

        $this->data = [
            'banner' => $this->getBanner($this->eventContact->event,'banner_large'),
            'link' => route('pdf-printer', ['type' => 'eventConfirmation', 'identifier' => $this->identifier]),
        ];
    }

    public function email(): string|array
    {
        return $this->accountAccessor->getEmail();
    }

    public function subject(): string
    {
        return __('ui.send_event_contact_confirmation.subject',
            ['event_name' => $this->eventContact->event->texts->name]
        );
    }

    public function view(): string
    {
        return 'mails.mailer.event-contact-confirmation';
    }
}
