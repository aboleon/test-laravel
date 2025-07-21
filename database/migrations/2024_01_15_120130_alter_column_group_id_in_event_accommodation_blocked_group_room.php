<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('event_accommodation_blocked_group_room');

        Schema::create('event_accommodation_blocked_group_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_accommodation_id');
            $table->foreign('event_accommodation_id', 'fk_event_accommodation_group_id')
                ->references('id')
                ->on('event_accommodation')
                ->cascadeOnDelete();
            $table->foreignId('event_group_id')->constrained('event_groups')->cascadeOnDelete();
            $table->string('group_key')->index();
            $table->date('date')->index();
            $table->foreignId('room_group_id')->constrained('event_accommodation_room_groups')->cascadeOnDelete();
            $table->unsignedInteger('total')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
