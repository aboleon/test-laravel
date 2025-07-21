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
        if (Schema::hasColumn('event_program_sessions', 'event_program_day_id')) {
            Schema::table('event_program_sessions', function (Blueprint $table) {
                $table->dropForeign('event_program_sessions_event_program_day_id_foreign');
                $table->dropColumn('event_program_day_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('event_program_day_id')->nullable();
            $table->foreign('event_program_day_id')
                ->references('id')->on('event_program_day_rooms')
                ->onDelete('cascade');
        });
    }
};
