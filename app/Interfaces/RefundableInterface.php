<?php

namespace App\Interfaces;

use App\Models\FrontTransaction;
use App\Models\Order;
use App\Models\PaymentTransaction;

interface RefundableInterface
{
    public function refundableAmount(): int;
    public function normalizedAmount(): int;
    public function refundableReason(): string;
    public function transaction(): FrontTransaction|PaymentTransaction;
    public function model(): string;
    public function id(): int;
    public function order(): Order;

}
