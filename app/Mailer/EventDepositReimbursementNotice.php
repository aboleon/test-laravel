<?php

namespace App\Mailer;

use App\Accessors\EventDepositAccessor;
use App\Enum\OrderType;
use App\Mail\MailerMail;
use App\Models\EventContact;
use App\Models\EventManager\Sellable;
use App\Models\Order\EventDeposit;
use App\Traits\EventCommons;
use Exception;
use Illuminate\Support\Facades\Mail;

class EventDepositReimbursementNotice extends MailerAbstract
{
    use EventCommons;

    private EventContact $eventContact;


    public float $amount;
    public string $eventName;
    public string $depositName;
    public string $beneficiaryName;
    public string $banner;

    /**
     * @throws Exception
     */
    public function send()
    {
        $eventDeposit       = $this->model;

        if (! $eventDeposit instanceof EventDeposit) {
            throw new Exception("EventDeposit not found");
        }

        $accessor           = (new EventDepositAccessor($eventDeposit));
        $this->eventContact = $eventDeposit->eventContact;

        $this->amount    = $accessor->amount();
        $this->eventName = $accessor->eventName();


        $this->depositName = match ($eventDeposit->shoppable_type) {
            Sellable::class => $eventDeposit->sellable->title,
            OrderType::GRANTDEPOSIT->value => "Caution pour grant",
            default => throw new Exception("Unknown shoppable type ".$eventDeposit->shoppable_type)
        };

        $this->beneficiaryName = $accessor->accountNames();
        $this->banner          = $this->getBanner($eventDeposit->event,'banner_large');

        return Mail::send(new MailerMail($this));
    }

    public function email(): string|array
    {
        return $this->eventContact->user->email;
    }


    public function subject(): string
    {
        return $this->eventName.' - '. __('front/order.deposit_reimbursement');
    }

    public function view(): string
    {
        return 'mails.mailer.event-deposit-reimbursement-notice';
    }
}
