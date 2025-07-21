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
            $table->dropColumn('po');
            $table->dropColumn('notes');
            $table->dropColumn('terms');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->text('po')->nullable();
            $table->text('note')->nullable();
            $table->text('terms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoices', function (Blueprint $table) {
            $table->text('po')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('po');
            $table->dropColumn('note');
            $table->dropColumn('terms');
        });
    }
};
