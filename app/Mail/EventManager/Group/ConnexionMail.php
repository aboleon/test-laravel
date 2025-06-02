<?php

namespace App\Mail\EventManager\Group;

use App\Accessors\Users;
use App\Models\Event;
use App\Models\EventManager\Transport\EventTransport;
use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConnexionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Event $event,
        public Group $group
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
            subject: 'Connectez-vous à votre compte',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $lang = Users::guessPreferredLang($this->user);
        if (!in_array($lang, ['fr', 'en'])) {
            $lang = 'fr';
        }

        return new Content(
            view: 'mails.eventManager.group.connexion-mail-' . $lang,
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
