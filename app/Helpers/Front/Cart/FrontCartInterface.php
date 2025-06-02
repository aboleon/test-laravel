<?php

namespace App\Helpers\Front\Cart;


interface FrontCartInterface
{

    public function isEmpty(): bool;

    public function getTotalTtc(): float;

    public function getTotalNet(): float;

    public function clearCart(bool $replenishStock = true): void;
}
