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
        Schema::create('events_contacts_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('events_contacts_id')
                ->constrained('events_contacts')
                ->cascadeOnDelete();

            $table->string('order_number')->unique();
            $table->timestamp('order_date');
            $table->unsignedInteger('total_price');

            $table->unsignedInteger('tax_amount');
            $table->unsignedInteger('total_without_tax');
            $table->unsignedInteger('amount_paid')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_contacts_orders');
    }
};
