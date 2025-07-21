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
        Schema::table('events_contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('events_contacts', 'order_cancellation')) {
                $table->boolean('order_cancellation')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_contacts', function (Blueprint $table) {
            if (Schema::hasColumn('events_contacts', 'order_cancellation')) {
                $table->dropColumn('order_cancellation');
            }
        });
    }
};
