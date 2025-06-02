<?php

namespace App\Mail;

use App\Accessors\EventDepositAccessor;
use App\Mailer\MailerAbstract;
use App\Models\CustomPaymentCall;
use Illuminate\Support\Facades\Mail;
use MetaFramework\Traits\Responses;

class EventDepositPaymentResponse extends MailerAbstract
{

    use Responses;

    public string $locale;

    public EventDepositAccessor $accessor;

    public function __construct(public CustomPaymentCall $paymentCall)
    {
        $this->accessor = new EventDepositAccessor($this->paymentCall->shoppable);
    }

    public function paymentState(): string
    {
        return $this->paymentCall->state;
    }

    public function amount(): string
    {
        return $this->paymentCall->total;
    }

    public function send(): array
    {
        Mail::send(new MailerMail($this));
    }

    public function email(): string
    {
        return $this->accessor->eventAdminEmail();
    }

    public function subject(): string
    {
        return $this->accessor->accountNames()." a payé sa caution PEC";
    }

    public function view(): string
    {
        return 'mails.mailer.event-deposit-payment-response';
    }
}
