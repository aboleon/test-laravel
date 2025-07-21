<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_cart', function (Blueprint $table) {
            if (!Schema::hasColumn('order_cart', 'order_id')) {
                $table->foreignId('order_id')->after('id')->constrained('orders')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart', function (Blueprint $table) {
            $table->dropForeign('order_cart_order_id_foreign');
            $table->dropColumn('order_id');
        });
    }
};
