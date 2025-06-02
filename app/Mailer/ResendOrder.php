<?php

namespace App\Mailer;

use App\Accessors\OrderAccessor;
use App\Actions\Front\AutoConnectHelper;
use App\Mail\MailerMail;
use App\Models\Order;
use App\Traits\EventCommons;
use Illuminate\Support\Facades\Mail;

class ResendOrder extends MailerAbstract
{
    use EventCommons;

    public array $data = [];
    private ?Order $order = null;
    private ?OrderAccessor $orderAccessor = null;

    public function send()
    {
        return Mail::send(new MailerMail($this));
    }

    public function setData(): void
    {
        $this->order = $this->model ?: Order::where('uuid', $this->identifier)->first();

        if ($this->order) {
            $this->orderAccessor = (new OrderAccessor($this->order));
        } else {
            abort(404, "Order not found with uuid ".$this->identifier);
        }

        $this->data = [
            'order'   => $this->order,
            'orderAccessor'   => $this->orderAccessor,
            'banner'  => $this->getBanner($this->order->event,'banner_large'),
            'connect_link' => AutoConnectHelper::generateAutoConnectUrlForEventContact($this->order->getEventContact()),
        ];
    }

    public function email(): string|array
    {
        return $this->orderAccessor->invoiceableMail();
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function subject(): string
    {
        return 'Relance de votre commande Divine ID';
    }

    public function view(): string
    {
        return 'mails.mailer.resend-order';
    }
}
