<?php

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
        Schema::table('order_refunds', function (Blueprint $table) {
           $table->foreignId('payment_id')->after('order_id')->nullable()->constrained('order_payments')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_refunds', function (Blueprint $table) {
            $table->dropForeign('order_refunds_payment_id_foreign');
            $table->dropColumn('payment_id');
        });
    }
};
