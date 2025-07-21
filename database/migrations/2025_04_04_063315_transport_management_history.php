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
            $table->enum('management_history', DesiredTransportManagement::keys())->nullable()->after('desired_management');
            $table->timestamp('management_mail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_transports', function (Blueprint $table) {
            $table->dropColumn('management_history');
            $table->dropColumn('management_mail');
        });
    }
};
