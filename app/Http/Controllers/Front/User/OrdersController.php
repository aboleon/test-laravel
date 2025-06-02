<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\EventAccessor;
use App\Accessors\EventContactAccessor;
use App\Accessors\Front\FrontCache;
use App\Accessors\Front\Sellable\Accommodation;
use App\Accessors\Front\Sellable\Service;
use App\DataTables\Front\MyOrdersDataTable;
use App\Enum\OrderCartType;
use App\Enum\OrderSource;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use App\Models\Order;

class OrdersController extends EventBaseController
{
    public function index(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.orders_title'));

        $eventContact         = FrontCache::getEventContact();
        $eventContactAccessor = (new EventContactAccessor())->setEventContact($eventContact);
        $dataTable            = new MyOrdersDataTable($eventContact);

        $attributed = $eventContactAccessor->getAttributionSummary();

        return $dataTable->render('front.user.orders.orders', [
            'eventAccessor' => (new EventAccessor($event)),
            "orderAmount"   => $eventContactAccessor->getAllRemainingPayments(),
            "dataTable"     => $dataTable,
            'attributed'    => $attributed,
        ]);
    }

    public function edit(string $locale, Event $event, Order $order)
    {
        Seo::generator(__('front/seo.orders_title'));

        return view('front.user.orders.edit', [
            "event"                     => $event,
            "order"                     => $order,
            'order_not_linked_to_event' => $order->event_id != $event->id,
        ]);
    }
}
