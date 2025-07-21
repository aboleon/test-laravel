<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_invoices_cancels', function (Blueprint $table) {
            if (false === Schema::hasColumn('order_invoices_cancels', 'name')) {
                $table->string('name')->nullable()->after('invoice_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoices_cancels', function (Blueprint $table) {
            if (true === Schema::hasColumn('order_invoices_cancels', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
