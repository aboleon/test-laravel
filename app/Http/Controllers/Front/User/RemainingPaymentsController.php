<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\EventAccessor;
use App\Accessors\EventContactAccessor;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use Exception;

class RemainingPaymentsController extends EventBaseController
{
    public function index(string $locale, Event $event)
    {
        try {
            $eventContact = $this->getEventContact();
        } catch (Exception $exception) {
            return view('errors.404')->with(['user' => auth()->user(), 'event' => $event, 'exception' => $exception]);

        }

        Seo::generator(__('front/seo.remaining_payments_title'));

        $eventContactAccessor = (new EventContactAccessor())
            ->setEventContact($eventContact);

        $remainingOrders = $eventContactAccessor->getOrdersWithRemainingPayments();
        $assignedOrders = $eventContactAccessor->getAssignedOrdersWithRemainingPayments();

        return view('front.user.remaining-payments.remaining-payments', [
            'has_any_orders' =>$remainingOrders->isNotEmpty() or $assignedOrders->isNotEmpty(),
            'event' => $event,
            'eventContact' => $eventContact,
            'remainingOrders' => $remainingOrders,
            'assignedOrders' => $assignedOrders,
            'eventAccessor' => new EventAccessor($event),
        ]);
    }
}
