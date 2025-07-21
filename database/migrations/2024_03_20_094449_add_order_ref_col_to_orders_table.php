<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function ($table) {
            if (!Schema::hasColumn('orders', 'order_ref')) {
                $table->uuid('order_ref')->nullable()->unique();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function ($table) {
            if (Schema::hasColumn('orders', 'order_ref')) {
                $table->dropColumn('order_ref');
            }
        });
    }
};
