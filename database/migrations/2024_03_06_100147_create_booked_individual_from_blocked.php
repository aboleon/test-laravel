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
        Schema::create('booked_individual_from_blocked', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_group_id')->constrained('event_accommodation_room_groups')->restrictOnDelete();
            $table->foreignId('participation_type_id')->nullable()->references('id')->on('participation_types')->restrictOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booked_individual_from_blocked');
    }
};
