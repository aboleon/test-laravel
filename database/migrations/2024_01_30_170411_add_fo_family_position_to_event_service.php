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
        Schema::table('event_service', function (Blueprint $table) {
            if (false === Schema::hasColumn('event_service', 'fo_family_position')) {
                $table->unsignedSmallInteger('fo_family_position')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_service', function (Blueprint $table) {
            if (Schema::hasColumn('event_service', 'fo_family_position')) {
                $table->dropColumn('fo_family_position');
            }
        });
    }
};
