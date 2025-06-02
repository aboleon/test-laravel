<?php

namespace App\Mailer;

use App\Enum\OrderType;
use App\Models\EventContact;
use App\Models\EventManager\Sellable;
use App\Models\Order\EventDeposit;
use App\Traits\EventCommons;
use Exception;

class DepositInvoice extends MailerAbstract
{
    use EventCommons;

    private EventContact $eventContact;


    public float $amount;
    public string $eventName;
    public string $depositName;
    public string $beneficiaryName;
    public ?string $banner;
    public string $orderUuid;

    /**
     * @throws Exception
     */
    public function setData(): self
    {
        if ( ! $this->model instanceof EventDeposit) {
            throw new Exception("EventDeposit not found ");
        }

        $eventDeposit    = $this->model;
        $this->orderUuid = $eventDeposit->order->uuid;

        $this->eventContact = $eventDeposit->eventContact;

        if ( !$this->eventContact) {
            throw new Exception("EventContact not found with id ".$eventDeposit->event_contact_id);
        }

        $this->amount    = $eventDeposit->total_net + $eventDeposit->total_vat;
        $this->eventName = $eventDeposit->event->texts->name;


        $this->depositName = match ($eventDeposit->shoppable_type) {
            Sellable::class => $eventDeposit->sellable->title,
            OrderType::GRANTDEPOSIT->value => "Caution pour grant",
            default => throw new Exception("Unknown shoppable type ".$eventDeposit->shoppable_type)
        };

        $u                     = $this->eventContact->user;
        $this->beneficiaryName = $u->first_name.' '.$u->last_name;
        $this->banner          = $this->getBanner($eventDeposit->event,'banner_large');

        return $this;
    }

    public function email(): string|array
    {
        return $this->eventContact->user->email;
    }


    public function subject(): string
    {
        return $this->eventName.' - Facture de caution';
    }

    public function view(): string
    {
        return 'mails.mailer.deposit-invoice';
    }


    public function attachments(): array
    {
        return [
            [
                'type' => 'binary',
                'file' => (new \App\Printers\PDF\Invoice($this->orderUuid))->binary(),
                'as'   => 'invoice.pdf',
                'mime' => 'application/pdf',
            ],
        ];
    }
}
