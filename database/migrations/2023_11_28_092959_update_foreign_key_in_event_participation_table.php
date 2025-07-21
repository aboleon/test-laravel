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
        Schema::table('event_participation', function (Blueprint $table) {
            $table->dropForeign(['participation_id']);
            $table->foreign('participation_id')
                ->references('id')->on('participation_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('event_participation', function (Blueprint $table) {
            $table->dropForeign(['participation_id']);
            $table->foreign('participation_id')
                ->references('id')->on('dictionnary_entries')
                ->onDelete('cascade');
        });
    }
};
