<?php

use App\Enum\PaymentMethod;
use App\Enum\PaymentPurpose;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained("order_invoices")->cascadeOnDelete();
            $table->date('date')->index();
            $table->unsignedInteger('price');
            $table->enum('payment_method', PaymentMethod::keys())->index();
            $table->string('authorization_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string("bank")->nullable();
            $table->string("issuer")->nullable();
            $table->string("check_number")->nullable();
//            $table->enum('payment_purpose', PaymentPurpose::keys())->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
