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
            if (true === Schema::hasColumn('event_program_interventions', 'group_id')) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            }
            if (false === Schema::hasColumn('event_program_interventions', 'sponsor_id')) {
                $table->foreignId('sponsor_id')->nullable()->constrained('dictionnary_entries')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_interventions', function (Blueprint $table) {
            if (Schema::hasColumn('event_program_interventions', 'sponsor_id')) {
                $table->dropForeign(['sponsor_id']);
                $table->dropColumn('sponsor_id');
            }
        });
    }
};
