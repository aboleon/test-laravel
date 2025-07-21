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
        Schema::create('order_cart_accommodation_cancellations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedInteger('quantity');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('cancelled_at');
        });

        Schema::table('order_cart_accommodation_cancellations', function (Blueprint $table) {
            $table->foreign('cart_id', 'acc_cart_fk')
            ->references('id')->on('order_cart_accommodation')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cart_accommodation_cancellations');
    }
};
