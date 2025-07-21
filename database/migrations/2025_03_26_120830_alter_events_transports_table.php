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
            $table->renameColumn('departure_price','ticket_price');
            $table->dropColumn('return_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_transports', function (Blueprint $table) {

            $table->renameColumn('ticket_price','departure_price');
            $table->unsignedInteger('return_price')->nullable()->after('return_participant_comment');
        });
    }
};
