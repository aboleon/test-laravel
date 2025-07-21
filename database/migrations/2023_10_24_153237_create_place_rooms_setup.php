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
        Schema::create('place_rooms_setup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_room_id')->nullable()->constrained('place_rooms')->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('capacity')->nullable();
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_rooms_setup');
    }
};
