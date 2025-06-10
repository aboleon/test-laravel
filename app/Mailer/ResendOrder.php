<?php

namespace App\Mailer;

use App\Accessors\OrderAccessor;
use App\Actions\Front\AutoConnectHelper;
use App\Mail\MailerMail;
use App\Models\Order;
use App\Traits\EventCommons;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ResendOrder extends MailerAbstract
{
    use EventCommons;

    public array $data = [];
    private ?Order $order = null;
    private ?OrderAccessor $orderAccessor = null;

    public function send(): void
    {
        try {
            Mail::send(new MailerMail($this));
            $this->responseSuccess("L'e-mail de relance pour la commande #".$this->order->id." a été envoyé.");
        } catch (Throwable $e) {
            $this->responseException($e, "L'e-mail de relance pour la commande #".$this->order->id." n'a pas pu être envoyé.");
        }
    }

    public function setData(): void
    {
        $this->order = $this->model ?: Order::where('id', $this->identifier)->first();

        if ($this->order) {
            $this->orderAccessor = (new OrderAccessor($this->order));
        } else {
            abort(404, "La commande  #".$this->identifier ." n'a pas été trouvée.");
        }

        if ($this->orderAccessor->isPaid()) {
            $this->responseWarning("La commande #".$this->order->id ." a déjà été soldée. E-mail de relance non envoyé.");
        }

        $this->data = [
            'order'         => $this->order,
            'orderAccessor' => $this->orderAccessor,
            'banner'        => $this->getBanner($this->order->event, 'banner_large'),
            'connect_link'  => AutoConnectHelper::generateAutoConnectUrlForEventContact($this->order->getEventContact()),
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
