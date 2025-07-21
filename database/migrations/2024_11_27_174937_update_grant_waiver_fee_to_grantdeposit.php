<?php

use App\Enum\OrderType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::update('UPDATE front_cart_lines SET shoppable_type = ? WHERE shoppable_type = ?', [
            OrderType::GRANTDEPOSIT->value,
            'grant_waiver_fee',
        ]);

        DB::update('UPDATE event_deposits SET shoppable_type = ? WHERE shoppable_type = ?', [
            OrderType::GRANTDEPOSIT->value,
            'grant_waiver_fee',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grantdeposit', function (Blueprint $table) {
            DB::update('UPDATE front_cart_lines SET shoppable_type = ? WHERE shoppable_type = ?', [
                'grant_waiver_fee',
                OrderType::GRANTDEPOSIT->value,
            ]);
            DB::update('UPDATE event_deposits SET shoppable_type = ? WHERE shoppable_type = ?', [
                'grant_waiver_fee',
                OrderType::GRANTDEPOSIT->value,
            ]);
        });
    }
};
