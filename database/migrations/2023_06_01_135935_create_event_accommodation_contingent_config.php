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
        Schema::create('event_accommodation_contingent_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contingent_id')->constrained('event_accommodation_contingent')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('event_accommodation_room')->cascadeOnDelete();
            $table->boolean('published')->nullable()->index();
            $table->unsignedInteger('buy')->nullable();
            $table->unsignedInteger('sell')->nullable();
            $table->boolean('pec')->nullable();
            $table->unsignedInteger('pec_allocation')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('event_accommodation_service')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_accommodation_contingent_config');
    }
};
