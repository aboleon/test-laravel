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
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->dropColumn('address_line_1');
            $table->dropColumn('address_line_2');
            $table->dropColumn('address_line_3');
            $table->dropColumn('email');
            $table->renameColumn('zip', 'postal_code');
            $table->string('street_number');
            $table->string('route');
            $table->text('complementary');
            $table->text('text_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->text('address_line_1');
            $table->text('address_line_2');
            $table->text('address_line_3');
            $table->text('email');
            $table->renameColumn('postal_code', 'zip');
            $table->dropColumn('street_number');
            $table->dropColumn('route');
            $table->dropColumn('complementary');
            $table->dropColumn('text_address');
        });
    }
};
