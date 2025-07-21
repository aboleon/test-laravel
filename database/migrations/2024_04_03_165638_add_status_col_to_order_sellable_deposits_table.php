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
        Schema::table('order_sellable_deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('order_sellable_deposits', 'status')) {
                $table->enum('status', EventDepositStatus::keys())->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_sellable_deposits', function (Blueprint $table) {
            if (Schema::hasColumn('order_sellable_deposits', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
