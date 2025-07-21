<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('event_groups', 'main_contact_id')) {
                $table->foreignId('main_contact_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_groups', function (Blueprint $table) {
            if (Schema::hasColumn('event_groups', 'main_contact_id')) {
                $table->dropForeign(['main_contact_id']);
                $table->dropColumn('main_contact_id');
            }
        });
    }
};
