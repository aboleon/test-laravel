<?php

use App\Enum\DesiredTransportManagement;
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
            if (false === Schema::hasColumn('event_transports', 'request_completed')) {
                $table->boolean('request_completed')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_transports', function (Blueprint $table) {
            if (Schema::hasColumn('event_transports', 'request_completed')) {
                $table->dropColumn('request_completed');
            }

        });
    }
};
