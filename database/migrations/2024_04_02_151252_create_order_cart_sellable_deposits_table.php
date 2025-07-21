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
        Schema::create('order_cart_sellable_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('order_cart')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('event_sellable_service')->restrictOnDelete();
            $table->foreignId('vat_id')->nullable()->constrained('vat')->restrictOnDelete();
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('total_net');
            $table->unsignedInteger('total_vat');
            $table->foreignId('event_contact_id')->nullable();
            $table->foreign('event_contact_id', 'ocsd_beneficiary_contact_fk')->references('id')->on('events_contacts')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cart_sellable_deposits');
    }
};
