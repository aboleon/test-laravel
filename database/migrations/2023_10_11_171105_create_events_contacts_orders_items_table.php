<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events_contacts_orders_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('events_contacts_orders')
                ->cascadeOnDelete();

            $table->foreignId('event_sellable_service_id')
                ->constrained('event_sellable_service')
                ->cascadeOnDelete();

            $table->integer('quantity');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('total_price');
            $table->unsignedInteger('total_price_without_tax');
            $table->unsignedInteger('tax_amount');

            // keep track of what price what used, just in case
            $table->unsignedBigInteger('event_sellable_service_price_id')->nullable();
            $table->foreign('event_sellable_service_price_id', 'fk_service_price')
                ->references('id')
                ->on('event_sellable_service_prices')
                ->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_contacts_orders_items');
    }
};
