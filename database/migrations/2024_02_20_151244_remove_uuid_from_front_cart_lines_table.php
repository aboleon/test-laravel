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
            if (Schema::hasColumn('front_cart_lines', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
