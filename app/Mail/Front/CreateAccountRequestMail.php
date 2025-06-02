<?php


namespace App\Mail\Front;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreateAccountRequestMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $templateLocale;

    public function __construct(
        public string $eventName,
        public string $eventUrl,
        public ?string $eventMediaUrl,
        public string $createAccountUrl
    )
    {
        $this->templateLocale = app()->getLocale();
    }

    public function build()
    {

        return $this
            ->subject(__('front/mail.create_an_account_subject'))
            ->view('mails.front.' . $this->templateLocale . '.create-account-request')
            ->with([
                'createAccountUrl' => $this->createAccountUrl,
                'eventUrl' => $this->eventUrl,
                'eventName' => $this->eventName,
                'eventMediaUrl' => $this->eventMediaUrl,
            ]);
    }
}
