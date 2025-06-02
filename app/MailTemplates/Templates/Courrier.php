<?php

declare(strict_types=1);

namespace App\MailTemplates\Templates;

use App\Accessors\Accounts;
use App\MailTemplates\Contracts\Template;
use App\MailTemplates\Models\MailTemplate as Target;
use App\MailTemplates\Traits\MailTemplate;
use App\MailTemplates\Traits\Variables\AllVariables;
use App\Models\Event;
use App\Models\EventContact;
use App\Traits\EventCommons;

class Courrier implements Template
{
    use MailTemplate;
    use EventCommons;
    use AllVariables;

    private const string SIGNATURE = 'courrier';

    private const array VARIABLE_GROUPS = [
        \App\MailTemplates\Groups\Event::class,
        \App\MailTemplates\Groups\Group::class,
        \App\MailTemplates\Groups\Manager::class,
        \App\MailTemplates\Groups\Participant::class,
    ];

    private const array EVENT_RELATIONS = [
        'texts',
        'type',
        'admin.profile',
        'place.address',
        'adminSubs.profile',
        'pec.grantAdmin.profile'
    ];

    private const array EVENT_CONTACT_RELATIONS = [
        'profile',
        'account.address',
        'account.phones'
    ];

    public readonly ?string $banner;
    public readonly ?string $eventContactAddress;
    public readonly ?Accounts $accountAccessor;

    private array $attachmentConfig = [
        'file' => null,
        'options' => []
    ];

    public function __construct(
        public readonly Event $event,
        public readonly Target $template,
        public readonly ?EventContact $eventContact = null,
    ) {
        $this->loadRelations();
        $this->initializeBanner();
        $this->initializeEventContact();
    }

    public function signature(): string
    {
        return self::SIGNATURE;
    }

    public function variables(): array
    {
        return collect(self::VARIABLE_GROUPS)
            ->flatMap(fn(string $group) => $group::variables())
            ->flip()
            ->toArray();
    }

    public function summary(): ?string
    {
        // TODO: Implement summary generation
        // return View::make('mailtemplates.mail.mailtemplate', ['template' => $this->template])->render();
        return null;
    }

    public function setFilePath(string $file): self
    {
        $this->attachmentConfig['file'] = $file;
        return $this;
    }

    public function setFileOptions(array $options): self
    {
        $this->attachmentConfig['options'] = $options;
        return $this;
    }

    public function event(): Event
    {
        return $this->event;
    }

    public function template(): Target
    {
        return $this->template;
    }

    public function getAttachment(): ?array
    {
        return $this->attachmentConfig['file'] !== null
            ? $this->attachmentConfig
            : null;
    }

    private function loadRelations(): void
    {
        $this->event->load(self::EVENT_RELATIONS);

        if ($this->eventContact !== null) {
            $this->eventContact->load(self::EVENT_CONTACT_RELATIONS);
        }
    }

    private function initializeBanner(): void
    {
        $this->banner = $this->getBanner($this->event, 'banner_large');
    }

    private function initializeEventContact(): void
    {
        if ($this->eventContact === null) {
            $this->accountAccessor = null;
            $this->eventContactAddress = null;
            return;
        }

        $this->accountAccessor = new Accounts($this->eventContact->account);
        $this->eventContactAddress = $this->accountAccessor->billingAddress();
    }
}
