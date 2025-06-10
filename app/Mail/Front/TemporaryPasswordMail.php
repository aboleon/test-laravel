<?php


namespace App\Mail\Front;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TemporaryPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $temporaryPassword,
        public string $eventName,
        public string $eventUrl,
        public ?string $eventMediaUrl = null
    )
    {
    }

    public function build()
    {

        return $this
            ->subject(__('front/mail.temporary_password_subject'))
            ->view('mails.front.' . app()->getLocale() . '.temporary-password')
            ->with([
                'email' => $this->email,
                'password' => $this->temporaryPassword,
                'eventUrl' => $this->eventUrl,
                'eventName' => $this->eventName,
                'eventMediaUrl' => $this->eventMediaUrl,
            ]);
    }
}
