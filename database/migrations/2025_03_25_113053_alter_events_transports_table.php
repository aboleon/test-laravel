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
        Schema::table('event_transports', function (Blueprint $table) {
            $table->unsignedInteger('departure_price')->nullable()->after('departure_participant_comment');
            $table->unsignedInteger('return_price')->nullable()->after('return_participant_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_transports', function (Blueprint $table) {
            $table->dropColumn('departure_price');
            $table->dropColumn('return_price');
        });
    }
};
