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
            $table->dropForeign('order_sellable_deposits_order_id_foreign');
            $table->dropColumn('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
