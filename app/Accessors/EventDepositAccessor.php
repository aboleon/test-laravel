<?php

namespace App\Accessors;

use App\Models\Order\EventDeposit;
use Illuminate\Support\Facades\Crypt;

class EventDepositAccessor
{

    private Accounts $accountAccessor;
    private EventAccessor $eventAccessor;
    public string $locale;

    public function __construct(public EventDeposit $model)
    {
        $this->accountAccessor = new Accounts($this->model->eventContact->account);
        $this->eventAccessor   = new EventAccessor($this->model->event);
        $this->locale          = $this->accountAccessor->getLocale();

    }

    public function accountNames(): string
    {
        return $this->accountAccessor->names();
    }

    public function accountEmail(): string
    {
        return $this->accountAccessor->account->email;
    }

    public function eventName(): string
    {
        return $this->eventAccessor->eventName();
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function amount(): string
    {
        return $this->model->total_net + $this->model->total_vat;
    }

    public function paymentLink(): string
    {
        return route('custompayment.form', ['uuid' => Crypt::encryptString($this->model->paymentCall->id)]);
    }

    public function eventAdminEmail(): string
    {
            return $this->eventAccessor->adminEmail();
    }
}
