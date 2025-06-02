<?php

namespace App\Http\Controllers\Front\User;

use App\Enum\OrderAmendedType;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use App\Models\Order;
use App\Models\Order\Cart\AccommodationCart;
use Exception;
use Illuminate\Support\Facades\Auth;

class AccommodationController extends EventBaseController
{
    /**
     * @throws Exception
     */
    public function edit(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.accommodation'));
        return view('front.user.accommodation', $this->sharedData($event));
    }

    /**
     * @throws Exception
     */
    public function amendCart(string $locale, Event $event, AccommodationCart $cart)
    {
        Seo::generator(__('front/seo.accommodation'));
        return view('front.user.accommodation_amend',
            array_merge([
                "amend" => OrderAmendedType::CART->value,
                'amendable' => $cart,
            ]),
            $this->sharedData($event)
        );
    }

    /**
     * @throws Exception
     */
    public function amendOrder(string $locale, Event $event, Order $order)
    {
        Seo::generator(__('front/seo.accommodation'));

        return view('front.user.accommodation_amend',
            array_merge([
                "amend" => OrderAmendedType::ORDER->value,
                'amendable' => $order,
            ]),
            $this->sharedData($event)
        );
    }

    /**
     * @throws Exception
     */
    protected function sharedData(Event $event): array
    {
        return [
            'user' => Auth::getUser(),
            'eventContact' => $this->getEventContact(),
            'event' => $event,
        ];

    }
}
