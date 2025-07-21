<?php

use App\Enum\EventDepositStatus;
use App\Models\Order\EventDeposit;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        EventDeposit::whereNotNull('reimbursed_at')->update(['status' => EventDepositStatus::REFUNDED->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
