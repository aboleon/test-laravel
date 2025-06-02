<?php

namespace App\Mail\EventManager\Transport;

use App\Accessors\Users;
use App\Models\EventManager\Transport\EventTransport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransportDepartureInfoReady extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public EventTransport $transport
    )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->transport->eventContact->event->texts->subname . ': Vos billets et informations de départ sont prêts',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $lang = Users::guessPreferredLang($this->transport->eventContact->user);
        if (!in_array($lang, ['fr', 'en'])) {
            $lang = 'fr';
        }

        return new Content(
            view: 'mails.eventManager.transport.transport-departure-info-ready-' . $lang,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
