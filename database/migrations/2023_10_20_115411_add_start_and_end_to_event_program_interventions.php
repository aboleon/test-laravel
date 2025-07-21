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
        Schema::table('event_program_interventions', function (Blueprint $table) {
            if (!Schema::hasColumn('event_program_interventions', 'start')) {
                $table->timestamp('start')->nullable();
            }

            if (!Schema::hasColumn('event_program_interventions', 'end')) {
                $table->timestamp('end')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_interventions', function (Blueprint $table) {
            if (Schema::hasColumn('event_program_interventions', 'start')) {
                $table->dropColumn('start');
            }

            if (Schema::hasColumn('event_program_interventions', 'end')) {
                $table->dropColumn('end');
            }
        });
    }
};
