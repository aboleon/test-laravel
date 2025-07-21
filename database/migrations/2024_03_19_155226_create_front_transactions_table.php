<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('front_transactions', function ($table) {
            $table->id();
            $table->uuid('preorder_uuid')->unique();
            $table->uuid('order_uuid')->nullable()->unique();
            $table->unsignedBigInteger('event_contact_id')->nullable();
            $table->unsignedBigInteger('group_manager_event_contact_id')->nullable();
            $table->json('lines')->nullable();
            $table->unsignedBigInteger('total')->nullable();
            $table->string('transaction_reference');
            $table->string('transaction_return_code');
            $table->json('transaction_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('front_transactions');
    }
};
