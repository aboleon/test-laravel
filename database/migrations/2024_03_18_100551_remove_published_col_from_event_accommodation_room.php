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
        Schema::table('event_accommodation_room', function (Blueprint $table) {
            if (Schema::hasColumn('event_accommodation_room', 'published')) {
                $table->dropColumn('published');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_accommodation_room', function (Blueprint $table) {
            if (!Schema::hasColumn('event_accommodation_room', 'published')) {
                $table->boolean('published')->nullable()->default(true);
            }
        });
    }
};
