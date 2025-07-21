<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_cart_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('front_cart_lines', 'unit_ttc')) {
                $table->unsignedInteger('unit_ttc')->default(0)->after("shoppable_id");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_cart_lines', function (Blueprint $table) {
            if (Schema::hasColumn('front_cart_lines', 'unit_ttc')) {
                $table->dropColumn('unit_ttc');
            }
        });
    }
};
