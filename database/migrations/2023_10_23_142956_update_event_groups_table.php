<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('event_groups', function (Blueprint $table) {
            $table->foreignId('main_contact_id')->nullable()->after('group_id')->constrained('users')->onDelete('set null');
            $table->foreignId('main_contact_participation_type_id')->nullable()->after('main_contact_id')->constrained('participation_types')->onDelete('cascade');
            $table->unique(['group_id', 'event_id']);
        });
    }

    public function down()
    {
        Schema::table('event_groups', function (Blueprint $table) {
            $table->dropUnique(['group_id', 'event_id']);
            $table->dropForeign(['main_contact_participation_type_id']);
            $table->dropColumn('main_contact_participation_type_id');
            $table->dropForeign(['main_contact_id']);
            $table->dropColumn('main_contact_id');
        });
    }

};
