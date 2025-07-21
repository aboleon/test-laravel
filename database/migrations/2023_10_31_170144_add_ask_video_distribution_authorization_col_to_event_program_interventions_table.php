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
        Schema::table('event_program_interventions', function (Blueprint $table) {
            $table->boolean('ask_video_distribution_authorization')->nullable()->after('allow_video_distribution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_interventions', function (Blueprint $table) {
            $table->dropColumn('ask_video_distribution_authorization');
        });
    }
};
