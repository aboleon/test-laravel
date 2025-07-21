<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_preorders', function ($table) {
            if (Schema::hasColumn('front_preorders', 'total')) {
                $table->dropColumn('total');
            }
            if (!Schema::hasColumn('front_preorders', 'total_ttc')) {
                $table->unsignedBigInteger('total_ttc')->nullable();
            }
            if (!Schema::hasColumn('front_preorders', 'total_net')) {
                $table->unsignedBigInteger('total_net')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_preorders', function ($table) {
            if (Schema::hasColumn('front_preorders', 'total_ttc')) {
                $table->dropColumn('total_ttc');
            }
            if (Schema::hasColumn('front_preorders', 'total_net')) {
                $table->dropColumn('total_net');
            }
            if (!Schema::hasColumn('front_preorders', 'total')) {
                $table->unsignedBigInteger('total');
            }
        });
    }
};
