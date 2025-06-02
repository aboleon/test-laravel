<?php

namespace App\Http\Controllers\Front\User\Group;

use App\Accessors\Front\FrontCache;
use App\Accessors\OrderAccessor;
use App\DataTables\Front\MyGroupOrdersDataTable;
use App\Enum\OrderClientType;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use App\Models\Order;

class GroupOrdersController extends EventBaseController
{
    public function index(string $locale, Event $event)
    {
        $eventGroup = $this->getEventGroup($event);
        Seo::generator(__('front/seo.group_order_title'));

        $dataTable = new MyGroupOrdersDataTable($this->eventContact, $eventGroup);

        return $dataTable->render('front.user.groupv2.orders', [
            "event"     => $event,
            "dataTable" => $dataTable,
        ]);
    }


    public function edit(string $locale, Event $event, Order $order)
    {
        Seo::generator(__('front/seo.orders_title'));
        $groupId = FrontCache::isEventMainContact();
        $orderAccessor = new OrderAccessor($order);

        if ($groupId && $order->client_type == OrderClientType::GROUP->value && $order->client_id !=$groupId) {
            return view('front.user.groupv2.error')->with([
                'message' => __('front/errors.group_order')
            ]);
        }

        return view('front.user.groupv2.orders.edit', [
            'invoiceable'    => $orderAccessor->invoiceable(),
            'orderAccessor'  => $orderAccessor,
            "event"          => $event,
            "order"          => $order,
            "isNotYourOrder" => $groupId && $order->client_type == OrderClientType::GROUP->value && $order->client_id !=$groupId,
        ]);
    }
}
