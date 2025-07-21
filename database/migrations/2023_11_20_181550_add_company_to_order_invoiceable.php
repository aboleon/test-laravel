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
            $table->string('company')->after('account_id')->nullable();
            $table->string('vat_number')->after('company')->nullable();
            $table->dropForeign('order_invoiceable_account_id_foreign');
            $table->dropColumn('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->dropColumn('company');
            $table->dropColumn('vat_number');
            $table->foreignId('account_id')->constrained('users')->restrictOnDelete();
        });
    }
};
