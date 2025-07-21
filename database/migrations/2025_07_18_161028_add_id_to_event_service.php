<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_service', function (Blueprint $table) {
           $table->dropForeign('event_service_event_id_foreign');
            $table->dropForeign('event_service_service_id_foreign');

            $table->dropPrimary();
        });

        DB::statement('ALTER TABLE `event_service` ADD `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

        Schema::table('event_service', function (Blueprint $table) {
            $table->unique(['event_id', 'service_id'], 'event_service_unique');

            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');

            $table->foreign('service_id')
                ->references('id')
                ->on('dictionnary_entries');

            $table->boolean('enabled')->default(false);


        });

        DB::statement('UPDATE `event_service` SET `enabled`=1 where `enabled`=0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_service', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['service_id']);

            $table->dropUnique('event_service_unique');
        });

        Schema::table('event_service', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('event_service', function (Blueprint $table) {
            $table->primary(['event_id', 'service_id']);

            $table->foreign('event_id', 'event_service_event_id_foreign')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');

            $table->foreign('service_id', 'event_service_service_id_foreign')
                ->references('id')
                ->on('dictionnary_entries');

            $table->dropColumn('enabled');
        });
    }
};
