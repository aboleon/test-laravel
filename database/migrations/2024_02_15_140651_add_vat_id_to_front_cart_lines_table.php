<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_cart_lines', function ($table) {
            if (!Schema::hasColumn('front_cart_lines', 'vat_id')) {
                $table->foreignId('vat_id')->nullable()->constrained('vat')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_cart_lines', function ($table) {
            if (Schema::hasColumn('front_cart_lines', 'vat_id')) {
                $table->dropForeign(['vat_id']);
                $table->dropColumn('vat_id');
            }
        });
    }
};
