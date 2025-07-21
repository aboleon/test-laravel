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
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->text('address_line_1')->nullable()->change();
            $table->text('zip')->nullable()->change();
            $table->text('locality')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->text('address_line_1')->nullable(false)->change();
            $table->text('zip')->nullable(false)->change();
            $table->text('locality')->nullable(false)->change();
        });
    }
};
