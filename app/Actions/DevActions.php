<?php

namespace App\Actions;

use App\Models\CustomPaymentCall;
use App\Models\FrontCart;
use App\Models\FrontTransaction;
use App\Models\Order;
use App\Models\PaymentCall;
use Throwable;

class DevActions
{

    public static function deleteOrder(int|array $order_id): void
    {
        try {
            $order_id = is_int($order_id) ? [$order_id] : $order_id;

            FrontTransaction::whereIn('order_id', $order_id)->delete();
            PaymentCall::whereIn('order_id', $order_id)->delete();
           // CustomPaymentCall::whereIn('order_id', $order_id)->delete();
            FrontCart::whereIn('order_id', $order_id)->delete();
            Order::whereIn('id', $order_id)->delete();

            d("Order ".implode(', ', $order_id)." deleted");
        } catch (Throwable $exception) {
            d($exception->getMessage());
        }
    }
}
