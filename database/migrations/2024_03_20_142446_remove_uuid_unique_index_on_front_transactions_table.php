<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_transactions', function ($table) {
            $table->dropUnique(['preorder_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_transactions', function ($table) {
            $table->unique('preorder_uuid');
        });
    }
};
