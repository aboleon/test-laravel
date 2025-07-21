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
        Schema::table('event_program_interventions', function (Blueprint $table) {
            if (Schema::hasColumn('event_program_interventions', 'place_room_id')) {
                $table->dropForeign(['place_room_id']);
                $table->dropColumn('place_room_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_interventions', function (Blueprint $table) {
            $table->unsignedBigInteger('place_room_id')->nullable();
            $table->foreign('place_room_id')->references('id')->on('place_rooms');
        });
    }
};
