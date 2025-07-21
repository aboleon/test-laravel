<?php

namespace Database\Seeders\Order;


use App\Models\EventContactOrder;
use App\Models\EventContactOrderItem;
use Illuminate\Database\Seeder;

class EventContactOrderSeeder extends Seeder
{
    private int $orderItemsCpt;
    private array $items;

    public function __construct()
    {
        $this->orderItemsCpt = 1;
        $this->items = [
            [
                'event_sellable_service_id' => 1,
                'event_sellable_service_price_id' => 1,
                'unit_price' => 12,
            ],
            [
                'event_sellable_service_id' => 2,
                'event_sellable_service_price_id' => 2,
                'unit_price' => 1200,
            ],
            [
                'event_sellable_service_id' => 2,
                'event_sellable_service_price_id' => 3,
                'unit_price' => 1300,
            ],
            [
                'event_sellable_service_id' => 3,
                'event_sellable_service_price_id' => 4,
                'unit_price' => 370,
            ],
        ];
    }

    public function run(): void
    {

        //--------------------------------------------
        // first order
        //--------------------------------------------
        $order = EventContactOrder::updateOrCreate(["id" => 1], [
            'events_contacts_id' => 10,
            'order_number' => 12000,
            'order_date' => '2023-10-12 09:00:00',
            'total_price' => "0", // computed later
            'total_without_tax' => "0", // computed later
            'tax_amount' => "10",
            'amount_paid' => "0",
        ]);
        $this->addOrderItems($order, [0 => 1, 1 => 1, 3 => 1]);

        //--------------------------------------------
        // second order
        //--------------------------------------------
        $order = EventContactOrder::updateOrCreate(["id" => 2], [
            'events_contacts_id' => 10,
            'order_number' => 12001,
            'order_date' => '2023-10-12 09:10:00',
            'total_price' => "0", // computed later
            'total_without_tax' => "0", // computed later
            'tax_amount' => "10",
            'amount_paid' => "0",
        ]);
        $this->addOrderItems($order, [0 => 3]);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function addOrderItems(EventContactOrder $order, array $itemIndexesToQuantities)
    {

        $total = 0;
        foreach ($itemIndexesToQuantities as $index => $quantity) {
            $itemData = $this->items[$index];

            $itemTotal = $itemData['unit_price'] * $quantity;
            $total += $itemTotal;


            EventContactOrderItem::updateOrCreate(["id" => $this->orderItemsCpt++], [
                'order_id' => $order->id,
                'event_sellable_service_id' => $itemData['event_sellable_service_id'],
                'quantity' => $quantity,
                'unit_price' => $itemData['unit_price'],
                'total_price' => $itemData['unit_price'] * $quantity,
                'total_price_without_tax' => $itemData['unit_price'] * $quantity,
                'tax_amount' => 0,
                'event_sellable_service_price_id' => $itemData['event_sellable_service_price_id'],
            ]);
        }

        $order->update([
            'total_price' => $total,
            'total_without_tax' => $total - $order->tax_amount,
        ]);
    }
}