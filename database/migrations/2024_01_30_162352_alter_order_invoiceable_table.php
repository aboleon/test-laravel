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
            $table->string('street_number')->nullable()->change();
            $table->string('route')->nullable()->change();
            $table->text('complementary')->nullable()->change();
            $table->text('text_address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
