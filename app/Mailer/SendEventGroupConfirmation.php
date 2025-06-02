<?php

namespace App\Mailer;


use App\Accessors\Accounts;
use App\Mail\MailerMail;
use App\Models\Event;
use App\Models\EventManager\EventGroup;
use App\Traits\EventCommons;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class SendEventGroupConfirmation extends MailerAbstract
{
    use EventCommons;

    public array $data = [];

    private ?EventGroup $eventGroup;

    private ?Accounts $accountAccessor;

    private ?Event $event = null;


    public function send()
    {
        return Mail::send(new MailerMail($this));
    }

    public function setData(): void
    {
        $id = decrypt($this->identifier);
        $this->eventGroup = EventGroup::where('id', $id)->with(['group', 'event', 'mainContact'])->first();

        if (!$this->eventGroup) {
            abort(404, "EventGroup not found with id " . $id);
        }

        $this->event = $this->eventGroup->event->load(['adminSubs', 'texts']);
        $mainContact = $this->eventGroup->mainContact;
        $this->accountAccessor = new Accounts($mainContact->account);
        App::setLocale($this->accountAccessor->getLocale());

        $this->data = [
            'banner' => $this->getBanner($this->event,'banner_large'),
            'link' => route('pdf-printer', ['type' => 'eventGroupConfirmation', 'identifier' => $this->identifier]),
        ];
    }

    public function email(): string|array
    {
        return $this->accountAccessor->getEmail();
    }

    public function subject(): string
    {
        return __('ui.send_event_group_confirmation.subject',
            ['event_name' => $this->event->texts->name]
        );
    }

    public function view(): string
    {
        return 'mails.mailer.event-group-confirmation';
    }
}
