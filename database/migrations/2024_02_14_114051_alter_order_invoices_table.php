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
        Schema::table('order_invoices', function(Blueprint $table) {
            $table->dropColumn('date');
            $table->timestamp('created_at');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoices', function(Blueprint $table) {
            $table->date('date');
            $table->dropColumn('created_at');
            $table->dropForeign('order_invoices_created_by_foreign');
            $table->dropColumn('created_by');
        });
    }
};
