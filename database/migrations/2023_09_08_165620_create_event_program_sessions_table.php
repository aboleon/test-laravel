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

            $table->boolean('is_online')->nullable();
            $table->longText('name')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('session_type_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->integer('duration')->nullable();
            $table->foreignId('place_room_id')->nullable()->constrained()->onUpdate('no action')->onDelete('cascade');


            $table->foreignId('group_id')->nullable()->constrained()->onUpdate('no action')->onDelete('cascade');


            $table->boolean('is_visible_in_front')->nullable();



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
