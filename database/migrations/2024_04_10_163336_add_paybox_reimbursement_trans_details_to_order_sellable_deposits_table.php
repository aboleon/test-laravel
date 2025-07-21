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
            $table->json('paybox_reimbursement_details')->nullable()->after('reimbursed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_sellable_deposits', function (Blueprint $table) {
            $table->dropColumn('paybox_reimbursement_details');
        });
    }
};
