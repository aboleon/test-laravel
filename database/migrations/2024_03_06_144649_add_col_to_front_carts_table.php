<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_carts', function ($table) {
            if (!Schema::hasColumn('front_carts', 'group_manager_event_contact_id')) {
                $table->foreignId('group_manager_event_contact_id')->nullable()->constrained('events_contacts')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_carts', function ($table) {
            if (Schema::hasColumn('front_carts', 'group_manager_event_contact_id')) {
                $table->dropForeign(['group_manager_event_contact_id']);
                $table->dropColumn('group_manager_event_contact_id');
            }
        });
    }
};
