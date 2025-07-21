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
        Schema::table('event_front_config', function (Blueprint $table) {
            if (false === Schema::hasColumn('event_front_config', 'speaker_pay_room')) {
                $table->boolean('speaker_pay_room')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_front_config', function (Blueprint $table) {
            if (true === Schema::hasColumn('event_front_config', 'speaker_pay_room')) {
                $table->dropColumn('speaker_pay_room');
            }
        });
    }
};
