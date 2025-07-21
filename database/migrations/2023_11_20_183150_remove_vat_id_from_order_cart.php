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
            if (Schema::hasColumn('order_cart', 'vat_id')) {
                $table->dropForeign('order_cart_vat_id_foreign');
                $table->dropColumn('vat_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart', function (Blueprint $table) {
            $table->foreignId('vat_id')->constrained('vat')->restrictOnDelete();
        });
    }
};
