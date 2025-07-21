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
        Schema::create('order_refunds_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained('order_refunds')->restrictOnDelete();
            $table->date('date');
            $table->unsignedInteger('amount');
            $table->text('object');
            $table->foreignId('vat_id')->constrained('vat')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_refunds_items');
    }
};
