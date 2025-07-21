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
        Schema::create('service_cart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('order_cart')->cascadeOnDelete();
            $table->date('date')->index();
            $table->foreignId('vat_id')->constrained('vat')->restrictOnDelete();
            $table->unsignedInteger('price_unit')->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('total_net')->default(0);
            $table->unsignedInteger('total_vat')->default(0);
            $table->unsignedInteger('total_pec')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_cart');
    }
};
