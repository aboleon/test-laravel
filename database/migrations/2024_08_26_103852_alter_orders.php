<?php

use App\Enum\OrderAmendedType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        if (Schema::hasColumn('orders', 'amended_accommodation_cart')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign('orders_amended_accommodation_cart_foreign');
                $table->dropColumn('amended_accommodation_cart');
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('amended_by_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('amended_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->enum('amend_type', OrderAmendedType::values())->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('amended_accommodation_cart')->nullable()->constrained('order_cart_accommodation')->nullOnDelete();;
        });

        if (Schema::hasColumn('orders', 'amended_order_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign('orders_amended_order_id_foreign');
                $table->dropColumn('amended_order_id');
                $table->dropColumn('amend_type');
            });
        }
    }
};
