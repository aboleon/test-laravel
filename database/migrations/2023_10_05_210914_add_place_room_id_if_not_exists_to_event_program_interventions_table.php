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
        Schema::table('event_program_interventions', function (Blueprint $table) {
            // Check if the column doesn't exist
            if (!Schema::hasColumn('event_program_interventions', 'place_room_id')) {
                $table->foreignId('place_room_id')->constrained()->onUpdate('no action')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_interventions', function (Blueprint $table) {
            if (Schema::hasColumn('event_program_interventions', 'place_room_id')) {
                $table->dropForeign(['place_room_id']);
                $table->dropColumn('place_room_id');
            }
        });
    }
};
