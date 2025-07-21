<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use \MetaFramework\Traits\MetaSchema;

    public function up()
    {
        if (Schema::hasColumn('event_program_sessions', 'event_program_day_room_id')) {
            Schema::table('event_program_sessions', function (Blueprint $table) {
                if (Schema::hasColumn('event_program_sessions', 'event_program_day_room_id') && $this->hasForeignKey($table, 'event_program_sessions_event_program_day_room_id_foreign'))
                    $table->dropForeign('event_program_sessions_event_program_day_room_id_foreign');
                $table->dropColumn('event_program_day_room_id');
            });
        }
        Schema::table('event_program_sessions', function (Blueprint $table) {
            $table->foreignId('event_program_day_room_id')->after('id')->constrained('event_program_day_rooms')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
