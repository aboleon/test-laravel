<?php

use App\Enum\EventProgramParticipantStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events_contacts_interventions', function (Blueprint $table) {
            if (!Schema::hasColumn('events_contacts_interventions', 'status')) {
                $table->enum('status', EventProgramParticipantStatus::keys())->default(EventProgramParticipantStatus::default());
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_contacts_interventions', function (Blueprint $table) {
            if (Schema::hasColumn('events_contacts_interventions', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
