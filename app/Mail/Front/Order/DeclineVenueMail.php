<?php


namespace App\Mail\Front\Order;

use App\Models\Account;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeclineVenueMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Event $event,
        public Account $account,
    ) {}

    public function build(): self
    {
        return $this
            ->subject($this->event->texts->name." - Demande d'annulation")
            ->view('mails.decline-venue')
            ->with([
                'event' => $this->event,
                'user'  => $this->account,
            ]);
    }
}
