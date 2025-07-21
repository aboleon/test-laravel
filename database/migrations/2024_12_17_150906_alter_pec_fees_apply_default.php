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
        DB::statement("ALTER TABLE `events_contacts` CHANGE COLUMN `pec_fees_apply` `pec_fees_apply` TINYINT(1) NULL DEFAULT '1'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `events_contacts` CHANGE COLUMN `pec_fees_apply` `pec_fees_apply` TINYINT(1) NULL DEFAULT NULL;");

    }
};
