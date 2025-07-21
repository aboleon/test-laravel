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
        Schema::table('order_payments', function (Blueprint $table) {

            $table->dropForeign('order_payments_invoice_id_foreign');
            $table->dropColumn('invoice_id');

            if (Schema::hasColumn('order_payments', 'payment_purpose')) {
                $table->dropColumn('payment_purpose');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->foreignId('invoice_id')->constrained("order_invoices")->cascadeOnDelete();
            $table->enum('payment_purpose', ['global', 'prestation', 'hebergement'])->index();
        });
    }
};
