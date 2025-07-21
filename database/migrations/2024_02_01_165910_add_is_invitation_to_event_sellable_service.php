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
        Schema::table('event_sellable_service', function (Blueprint $table) {
            if (!Schema::hasColumn('event_sellable_service', 'is_invitation')) {
                $table->boolean('is_invitation')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sellable_service', function (Blueprint $table) {
            if (Schema::hasColumn('event_sellable_service', 'is_invitation')) {
                $table->dropColumn('is_invitation');
            }
        });
    }
};
