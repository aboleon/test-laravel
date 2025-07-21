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
        Schema::create('event_accommodation_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_group_id')->constrained('event_accommodation_room_groups')->cascadeOnDelete();
            $table->foreignId('room_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->unsignedSmallInteger('capacity')->index();

            $table->unique(['room_group_id', 'room_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_accommodation_room');
    }
};
