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
        Schema::create('payment_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_call_id')->constrained('payment_call')->restrictOnDelete();
            $table->string('transaction_id');
            $table->string('transaction_call_id');
            $table->string('return_code');
            $table->longText('details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transaction');
    }
};
