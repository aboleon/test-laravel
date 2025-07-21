<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('front_preorders', function ($table) {
            $table->id();
            $table->foreignId('event_contact_id')->constrained('events_contacts')->cascadeOnDelete();
            $table->foreignId('group_manager_event_contact_id')->nullable()->constrained('events_contacts')->cascadeOnDelete();
            $table->json('lines')->nullable();
            $table->unsignedBigInteger('total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('front_preorders');
    }
};
