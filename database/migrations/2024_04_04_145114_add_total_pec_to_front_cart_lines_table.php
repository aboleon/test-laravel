<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_cart_lines', function ($table) {
            if (!Schema::hasColumn('front_cart_lines', 'total_pec')) {
                $table->unsignedInteger('total_pec')->nullable()->after("total_ttc");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_cart_lines', function ($table) {
            if (Schema::hasColumn('front_cart_lines', 'total_pec')) {
                $table->dropColumn('total_pec');
            }
        });
    }
};
