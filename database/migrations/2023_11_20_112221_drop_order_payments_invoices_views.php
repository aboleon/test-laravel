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
        DB::statement("DROP VIEW IF EXISTS order_invoices_cancels_view");
        DB::statement("DROP VIEW IF EXISTS order_payments_view");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
