<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_preorders', function ($table) {
            if (Schema::hasColumn('front_preorders', 'group_manager_event_contact_id')) {
                $table->dropForeign(['group_manager_event_contact_id']);
                $table->dropColumn('group_manager_event_contact_id');
            }
            if (!Schema::hasColumn('front_preorders', 'is_group_order')) {
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
