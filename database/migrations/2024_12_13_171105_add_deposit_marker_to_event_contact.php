<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `events_contacts`	DROP COLUMN `last_grant_id`");
        DB::statement("ALTER TABLE `events_contacts`ADD COLUMN `grant_deposit_not_needed` TINYINT(1) NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_contact', function (Blueprint $table) {

            DB::statement("ALTER TABLE `events_contacts` ADD COLUMN `last_grant_id` BIGINT UNSIGNED NULL DEFAULT NULL");
            DB::statement("ALTER TABLE `events_contacts`	DROP COLUMN `grant_deposit_not_needed`");
        });
    }
};
