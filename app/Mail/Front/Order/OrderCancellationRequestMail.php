<?php


namespace App\Mail\Front\Order;

use App\Models\Account;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancellationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public Order $order,
        public Account $account,
    ) {}

    public function build()
    {
        $editUrl = route("panel.manager.event.orders.edit", [
            'event' => $this->event,
            'order' => $this->order,
        ]);

        $subject = $this->event->texts->name." - Demande d'annulation d'une commande'";

        return $this
            ->subject($subject)
            ->view('mails.order-cancellation-request')
            ->with([
                'event'   => $this->event,
                'order'   => $this->order,
                'user'    => $this->account,
                'editUrl' => $editUrl,
            ]);
    }
}
