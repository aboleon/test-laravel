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
        Schema::table('event_program_sessions', function (Blueprint $table) {

            DB::statement('DELETE FROM event_program_sessions');
            $table->foreignId('event_program_day_room_id')
                ->after('id')
                ->constrained('event_program_day_rooms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_sessions', function (Blueprint $table) {
            $table->dropForeign(['event_program_day_room_id']);
            $table->dropColumn('event_program_day_room_id');
        });
    }
};
