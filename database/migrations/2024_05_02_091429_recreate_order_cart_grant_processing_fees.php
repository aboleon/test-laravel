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
        Schema::dropIfExists('order_cart_grant_processing_fees');
        Schema::create('order_cart_grant_processing_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('order_cart')->cascadeOnDelete();
            $table->foreignId('event_grant_id')->constrained('event_grant')->cascadeOnDelete();
            $table->foreignId('vat_id')->nullable()->constrained('vat')->nullOnDelete();
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('total_net');
            $table->unsignedInteger('total_vat');
            $table->foreignId('event_contact_id')->nullable();
            $table->foreign('event_contact_id', 'ocgpf2_beneficiary_contact_fk')->references('id')->on('events_contacts')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cart_grant_processing_fees');
    }
};
