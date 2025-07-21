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
        Schema::table('order_cart_service', function (Blueprint $table) {
            if (Schema::hasColumn('order_cart_service', 'cancelled')) {
                $table->dropColumn('cancelled');
            }
            if (!Schema::hasColumn('order_cart_service', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_service', function (Blueprint $table) {
            if (Schema::hasColumn('order_cart_service', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
            if (!Schema::hasColumn('order_cart_service', 'cancelled')) {
                $table->boolean('cancelled')->nullable();
            }
        });
    }
};
