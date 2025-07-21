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
            if (!Schema::hasColumn('event_program_sessions', 'place_room_id')) {
                $table->foreignId('place_room_id')->nullable()->after('session_type_id')->constrained('place_rooms')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('event_program_sessions', 'place_room_id')) {
                $table->dropForeign('event_program_sessions_place_room_id_foreign');
                $table->dropColumn('place_room_id');
            }
        });
    }
};
