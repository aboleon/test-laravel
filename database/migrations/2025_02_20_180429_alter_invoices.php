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
        Schema::table('order_invoices', function (Blueprint $table) {
            $table->dropColumn('cancelled_at');
            $table->dropColumn('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoices', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paid_at')->nullable();
        });
    }
};
