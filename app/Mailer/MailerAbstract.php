<?php

namespace App\Mailer;

use App\Interfaces\Mailer as MailerInterface;
use App\Mail\MailerMail;
use Illuminate\Support\Facades\Mail;
use MetaFramework\Traits\Responses;

abstract class MailerAbstract implements MailerInterface
{
    use Responses;

    protected ?object $model = null;
    protected ?string $identifier = null;

    public function setModel(object $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function send()
    {
        return Mail::send(new MailerMail($this));
    }
}
