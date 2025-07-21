<?php

use App\Enum\DesiredTransportManagement;
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
            $table->enum('desired_management', DesiredTransportManagement::keys())->nullable()->index();
            $table->boolean('transfer_requested')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_transports', function (Blueprint $table) {
            $table->dropColumn('desired_management');
            $table->dropColumn('transfer_requested');
        });
    }
};
