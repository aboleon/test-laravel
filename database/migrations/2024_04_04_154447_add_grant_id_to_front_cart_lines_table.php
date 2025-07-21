<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_cart_lines', function ($table) {
            if (!Schema::hasColumn('front_cart_lines', 'grant_id')) {
                $table->unsignedInteger('grant_id')->nullable()->after("total_pec");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_cart_lines', function ($table) {
            if (Schema::hasColumn('front_cart_lines', 'grant_id')) {
                $table->dropColumn('grant_id');
            }
        });
    }
};
