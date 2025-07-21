<?php

use App\Enum\PaymentCallState;
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
        Schema::table('payment_call', function (Blueprint $table) {
            $table->index('uuid');
            $table->enum('state', PaymentCallState::values())->default(PaymentCallState::OPEN->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_call', function (Blueprint $table) {
            $table->dropIndex('payment_call_uuid_index');
            $table->dropColumn('state');
        });
    }
};
