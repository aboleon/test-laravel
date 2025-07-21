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
        Schema::table('event_transports', function (Blueprint $table) {
            $table->renameColumn('transfer_shuttle_time', 'transfer_shuttle_time_departure');
            $table->renameColumn('transfer_info', 'transfer_info_departure');
        });


        Schema::table('event_transports', function (Blueprint $table) {
            $table->time('transfer_shuttle_time_departure')->change();
            $table->time('transfer_shuttle_time_return')->nullable();
            $table->text('transfer_info_return')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_transports', function (Blueprint $table) {
            $table->dropColumn(['transfer_shuttle_time_return', 'transfer_info_return']);
        });
        Schema::table('event_transports', function (Blueprint $table) {
            $table->string('transfer_shuttle_time_departure')->change(); // Or whatever the original type was
        });
        Schema::table('event_transports', function (Blueprint $table) {
            $table->renameColumn('transfer_shuttle_time_departure', 'transfer_shuttle_time');
            $table->renameColumn('transfer_info_departure', 'transfer_info');
        });
    }
};
