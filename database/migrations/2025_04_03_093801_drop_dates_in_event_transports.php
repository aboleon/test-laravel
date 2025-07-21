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
            $table->dropColumn('departure_end_date');
            $table->dropColumn('return_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_transports', function (Blueprint $table) {
            $table->date('departure_end_date');
            $table->date('return_end_date');
        });
    }
};
