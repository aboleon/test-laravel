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
        Schema::table('event_grant', function (Blueprint $table) {
            $table->unsignedInteger('pax_min')->nullable()->change();
            $table->unsignedInteger('pax_avg')->nullable()->change();
            $table->unsignedInteger('pax_max')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_grant', function (Blueprint $table) {

            $table->unsignedInteger('pax_min')->nullable(false)->change();
            $table->unsignedInteger('pax_avg')->nullable(false)->change();
            $table->unsignedInteger('pax_max')->nullable(false)->change();
        });
    }
};
