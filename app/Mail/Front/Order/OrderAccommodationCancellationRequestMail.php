<?php


namespace App\Mail\Front\Order;

use App\Models\Account;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderAccommodationCancellationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public Order $order,
        public Order\Cart\AccommodationCart $accommodationCart,
        public Account $account,
    ) {}

    public function build()
    {
        $editUrl = route("panel.manager.event.orders.edit", [
            'event' => $this->event,
            'order' => $this->order,
        ]);

        return $this
            ->subject(__('front/mail.create_an_account_subject'))
            ->view('mails.front.fr.order.order-accommodation-cancellation-request')
            ->with([
                'event'       => $this->event,
                'order'       => $this->order,
                'serviceCart' => $this->accommodationCart,
                'user'        => $this->account,
                'editUrl'     => $editUrl,
            ]);
    }
}
