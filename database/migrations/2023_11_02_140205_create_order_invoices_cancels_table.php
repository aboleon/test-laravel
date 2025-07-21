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
        Schema::create('order_invoices_cancels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained("order_invoices")->cascadeOnDelete();
            $table->foreignId('vat_id')->constrained("vat")->cascadeOnDelete();
            $table->date("date");
            $table->unsignedInteger("price_before_tax");
            $table->unsignedInteger("price_after_tax");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_invoices_cancels');
    }
};
