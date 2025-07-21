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
        Schema::table('events_texts', function (Blueprint $table) {
            // Add second_fo columns
            $table->text('second_home_subtitle')->nullable();
            $table->text('second_fo_home')->nullable();
            $table->text('second_fo_particpant_subtitle')->nullable();
            $table->text('second_fo_login_participant')->nullable();
            $table->text('second_fo_speaker_subtitle')->nullable();
            $table->text('second_fo_login_speaker')->nullable();
            $table->text('second_fo_industry_subtitle')->nullable();
            $table->text('second_fo_login_industry')->nullable();
            $table->text('second_fo_exhibitor_subtitle')->nullable();
            $table->text('second_fo_exhibitor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_texts', function (Blueprint $table) {
            // Remove second_fo columns
            $table->dropColumn([
                'second_home_subtitle',
                'second_fo_home',
                'second_fo_particpant_subtitle',
                'second_fo_login_participant',
                'second_fo_speaker_subtitle',
                'second_fo_login_speaker',
                'second_fo_industry_subtitle',
                'second_fo_login_industry',
                'second_fo_exhibitor_subtitle',
                'second_fo_exhibitor'
            ]);
        });
    }
}; 