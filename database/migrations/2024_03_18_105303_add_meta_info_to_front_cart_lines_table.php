<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_cart_lines', function ($table) {
            if (!Schema::hasColumn('front_cart_lines', 'meta_info')) {
                $table->json('meta_info')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_cart_lines', function ($table) {
            if (Schema::hasColumn('front_cart_lines', 'meta_info')) {
                $table->dropColumn('meta_info');
            }
        });
    }
};
