<?php

namespace App\Accessors\EventManager\Sellable;

use App\Models\EventManager\Sellable\Deposit;

class Deposits
{
    public static function getSellableDepositAmount(Deposit $deposit): float
    {
        return $deposit->amount;
    }

    public static function getSellableDepositNetAmount(Deposit $deposit): float
    {
        $netAmount = 0;
        if ($deposit->vat) {
            $netAmount += round($deposit->amount / (1 + $deposit->vat->rate / 100 / 100), 2);

        }
        return $netAmount;
    }
}