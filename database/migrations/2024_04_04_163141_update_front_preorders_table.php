<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_preorders', function ($table) {
            if (!Schema::hasColumn('front_preorders', 'total_pec')) {
                $table->unsignedInteger('total_pec')->nullable()->after('total_net');
            }
            if (!Schema::hasColumn('front_preorders', 'grant_id')) {
                $table->unsignedInteger('grant_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_preorders', function ($table) {
            if (Schema::hasColumn('front_preorders', 'total_pec')) {
                $table->dropColumn('total_pec');
            }
            if (Schema::hasColumn('front_preorders', 'grant_id')) {
                $table->dropColumn('grant_id');
            }
        });
    }
};
