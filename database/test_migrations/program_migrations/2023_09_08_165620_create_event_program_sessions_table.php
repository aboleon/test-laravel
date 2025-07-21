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
        Schema::create('event_program_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_program_day_room_id')->constrained('event_program_days')->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->longText('name')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_program_sessions');
    }
};
