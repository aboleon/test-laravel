<?php

use App\Enum\EventDepositStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_deposits', function (Blueprint $table) {
            $table->foreignId('grant_id')->nullable()->constrained('event_grant')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_deposits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('grant_id');
        });
    }
};
