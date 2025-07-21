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

        DB::statement('ALTER TABLE `event_grant_participation`
            DROP FOREIGN KEY `fk_event_grant_participation`;
        ');

        Schema::table('event_grant_participation', function (Blueprint $table) {
           $table->dropPrimary(['grant_id', 'participation_id']);
        });

        Schema::table('event_grant_participation', function (Blueprint $table) {
            $table->increments('id')->first();
        });

        DB::statement('ALTER TABLE `event_grant_participation`
            ADD CONSTRAINT `fk_event_grant_participation` FOREIGN KEY (`participation_id`) REFERENCES `participation_types` (`id`) ON UPDATE NO ACTION ON DELETE RESTRICT;
        ');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_grant_participation', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('event_grant_participation', function (Blueprint $table) {
            $table->primary(['grant_id', 'participation_id']);
        });
    }
};
