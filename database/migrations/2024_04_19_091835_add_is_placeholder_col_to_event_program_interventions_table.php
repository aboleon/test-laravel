<?php

use App\Enum\OrderOrigin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_program_interventions', function (Blueprint $table) {
            if (false === Schema::hasColumn('event_program_interventions', 'is_placeholder')) {
                $table->boolean('is_placeholder')->nullable()->after('is_catering');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_interventions', function (Blueprint $table) {
            if (Schema::hasColumn('event_program_interventions', 'is_placeholder')) {
                $table->dropColumn('is_placeholder');
            }
        });
    }
};
