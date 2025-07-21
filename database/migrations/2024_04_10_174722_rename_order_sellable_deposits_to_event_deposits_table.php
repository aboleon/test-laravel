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
        Schema::rename('order_sellable_deposits', 'event_deposits');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('event_deposits', 'order_sellable_deposits');
    }
};
