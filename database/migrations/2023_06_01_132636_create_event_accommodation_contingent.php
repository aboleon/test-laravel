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
        Schema::create('event_accommodation_contingent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_accommodation_id')->constrained('event_accommodation')->cascadeOnDelete();
            $table->foreignId('room_group_id')->constrained('event_accommodation_room_groups')->cascadeOnDelete();
            $table->date('date')->nullable()->index();
            $table->unsignedInteger('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_accommodation_contingent');
    }
};
