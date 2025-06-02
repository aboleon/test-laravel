<?php

namespace App\Mutators\Front\Cart;

use App\Accessors\Front\FrontCartAccessor;
use App\Models\FrontCart;
use Carbon\Carbon;

class FrontCartMutator
{
    public static function clearExpiringCarts(): void
    {
        FrontCart::query()
            ->whereNull('order_id')
            ->where('updated_at', '<', Carbon::now()->subSeconds(FrontCartAccessor::ORDER_TTL_SECONDS))->delete();
    }
}
