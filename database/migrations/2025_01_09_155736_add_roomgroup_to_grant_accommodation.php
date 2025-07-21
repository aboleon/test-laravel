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
        Schema::table('event_accommodation_grant', function (Blueprint $table) {
            DB::statement('DELETE from event_accommodation_grant');
            $table->unsignedBigInteger('room_group_id')->after('event_accommodation_id');
            $table
                ->foreign('room_group_id', 'roomgroup_grant_blocked_fk')
                ->references('id')
                ->on('event_accommodation_room_groups')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_accommodation_grant', function (Blueprint $table) {
            $table->dropForeign('roomgroup_grant_blocked_fk');
            $table->dropColumn('room_group_id');
        });
    }
};
