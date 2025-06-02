<?php


namespace App\Mail\Front;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WantToBeMainContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $eventName,
        public string $name,
        public string $address,
        public string $zip,
        public string $city,
        public string $country,
        public string $phone
    )
    {
    }

    public function build()
    {

        return $this
            ->subject("Demande de contact principal")
            ->view('mails.front.fr.want-to-be-main-contact')
            ->with([
                'userName' => $this->userName,
                'eventName' => $this->eventName,
                'name' => $this->name,
                'address' => $this->address,
                'zip' => $this->zip,
                'city' => $this->city,
                'country' => $this->country,
                'phone' => $this->phone,
            ]);
    }
}
