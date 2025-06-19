<?php

namespace App\Mutators\Front\Cart;

use App\Accessors\Front\FrontCartAccessor;
use App\Models\FrontCart;
use Carbon\Carbon;

class FrontCartMutator
{
    public static function clearExpiringCarts(): void
    {
        // TODO : should loop and delete because an error blocks everything
        /**
         * local.ERROR: SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (`divine-id`.`front_transactions`, CONSTRAINT `front_transactions_payment_call_id_foreign` FOREIGN KEY (`payment_call_id`) REFERENCES `front_payment_calls` (`id`)) (Connection: mysql, SQL: delete from `front_carts` where `order_id` is null and `updated_at` < 2025-06-15 23:30:17) {"exception":"[object] (Illuminate\\Database\\QueryException(code: 23000): SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (`divine-id`.`front_transactions`, CONSTRAINT `front_transactions_payment_call_id_foreign` FOREIGN KEY (`payment_call_id`) REFERENCES `front_payment_calls` (`id`)) (Connection: mysql, SQL: delete from `front_carts` where `order_id` is null and `updated_at` < 2025-06-15 23:30:17) at /var/www/vhosts/divine-id.wagaia.com/httpdocs/vendor/laravel/framework/src/Illuminate/Database/Connection.php:822)
         * [stacktrace]
         *
         *
         * SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (`divine-id`.`front_transactions`, CONSTRAINT `front_transactions_payment_call_id_foreign` FOREIGN KEY (`payment_call_id`) REFERENCES `front_payment_calls` (`id`)) at /var/www/vhosts/divine-id.wagaia.com/httpdocs/vendor/laravel/framework/src/Illuminate/Database/Connection.php:593)
         */
        FrontCart::query()
            ->whereNull('order_id')
            ->where('updated_at', '<', Carbon::now()->subSeconds(FrontCartAccessor::ORDER_TTL_SECONDS))->delete();
    }
}
