<?php


namespace App\Mail\Front\Order;

use App\Models\Account;
use App\Models\Event;
use App\Models\Order;
use App\Models\Order\Cart\ServiceCart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderServiceCancellationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public Order $order,
        public ServiceCart $serviceCart,
        public Account $account,
    ) {}

    public function build()
    {
        $editUrl = route("panel.manager.event.orders.edit", [
            'event' => $this->event,
            'order' => $this->order,
        ]);

        $subject = $this->event->texts->name." - Demande d'annulation de service";

        return $this
            ->subject($subject)
            ->view('mails.front.fr.order.order-service-cancellation-request')
            ->with([
                'event'       => $this->event,
                'order'       => $this->order,
                'serviceCart' => $this->serviceCart,
                'user'        => $this->account,
                'editUrl'     => $editUrl,
            ]);
    }
}
