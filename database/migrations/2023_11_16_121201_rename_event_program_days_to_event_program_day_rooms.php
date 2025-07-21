<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('event_program_day_rooms')) {
            Schema::rename('event_program_days', 'event_program_day_rooms');
        }

        Schema::table('event_program_day_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('event_program_day_rooms', 'room_id')) {
                DB::statement("DELETE FROM event_program_day_rooms");
                $table->foreignId('room_id')->constrained("place_rooms");
            }
            $table->dateTime('datetime_start')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_program_day_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('event_program_day_rooms', 'event_program_day_rooms_room_id_foreign')) {
                $table->dropForeign(['room_id']);
            }
            $table->dropColumn('room_id');
            $table->dateTime('datetime_start')->nullable()->change();
        });
        Schema::rename('event_program_day_rooms', 'event_program_days');
    }
};
