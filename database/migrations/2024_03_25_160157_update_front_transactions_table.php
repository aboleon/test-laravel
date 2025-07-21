<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_transactions', function ($table) {
            if (Schema::hasColumn('front_transactions', 'group_manager_event_contact_id')) {
                $table->dropColumn('group_manager_event_contact_id');
            }
            if (!Schema::hasColumn('front_transactions', 'is_group_order')) {
                $table->boolean('is_group_order')->nullable();
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
