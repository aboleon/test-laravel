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
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            if (!Schema::hasColumn('order_cart_accommodation', 'accompanying_details')) {
                $table->text('accompanying_details')->nullable();
            }
            if (!Schema::hasColumn('order_cart_accommodation', 'comment')) {
                $table->text('comment')->nullable();
            }
            if (!Schema::hasColumn('order_cart_accommodation', 'processing_fee_ttc')) {
                $table->unsignedInteger("processing_fee_ttc")->nullable();
            }
            if (!Schema::hasColumn('order_cart_accommodation', 'processing_fee_vat')) {
                $table->unsignedInteger("processing_fee_vat")->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            if (Schema::hasColumn('order_cart_accommodation', 'accompanying_details')) {
                $table->dropColumn('accompanying_details');
            }
            if (Schema::hasColumn('order_cart_accommodation', 'comment')) {
                $table->dropColumn('comment');
            }
            if (Schema::hasColumn('order_cart_accommodation', 'processing_fee_ttc')) {
                $table->dropColumn('processing_fee_ttc');
            }
            if (Schema::hasColumn('order_cart_accommodation', 'processing_fee_vat')) {
                $table->dropColumn('processing_fee_vat');
            }
        });
    }
};
