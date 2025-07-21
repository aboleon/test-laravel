<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_preorders', function ($table) {
            if (!Schema::hasColumn('front_preorders', 'grant_allocations')) {
                $table->json('grant_allocations')->nullable()->after('grant_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_preorders', function ($table) {
            if (Schema::hasColumn('front_preorders', 'grant_allocations')) {
                $table->dropColumn('grant_allocations');
            }
        });
    }
};
