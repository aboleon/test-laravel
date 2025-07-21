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
            if (!Schema::hasColumn('order_cart', 'price_unit')) {
                $table->unsignedInteger('price_unit')->default(0);
            }
            if (!Schema::hasColumn('order_cart', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1);
            }
            if (!Schema::hasColumn('order_cart', 'total_net')) {
                $table->unsignedInteger('total_net')->default(0);
            }
            if (!Schema::hasColumn('order_cart', 'total_vat')) {
                $table->unsignedInteger('total_vat')->default(0);
            }
            if (!Schema::hasColumn('order_cart', 'total_pec')) {
                $table->unsignedInteger('total_pec')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart', function (Blueprint $table) {
            if (Schema::hasColumn('order_cart', 'price_unit')) {
                $table->dropColumn('price_unit');
            }
            if (Schema::hasColumn('order_cart', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('order_cart', 'total_net')) {
                $table->dropColumn('total_net');
            }
            if (Schema::hasColumn('order_cart', 'total_vat')) {
                $table->dropColumn('total_vat');
            }
            if (Schema::hasColumn('order_cart', 'total_pec')) {
                $table->dropColumn('total_pec');
            }
        });
    }
};
