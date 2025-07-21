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
        Schema::dropIfExists('events_contacts_interventions');
        Schema::create('events_contacts_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('events_contacts_id')
                ->constrained('events_contacts', indexName: 'eci_events_contacts_id')
                ->onDelete('CASCADE');

            $table->foreignId('event_program_intervention_id')
                ->constrained('event_program_interventions', indexName: 'eci_epi_id')
                ->onDelete('CASCADE');

            $table->enum('status', EventProgramParticipantStatus::keys())->default(EventProgramParticipantStatus::default());
            $table->boolean('allow_pdf_distribution')->nullable();
            $table->boolean('allow_video_distribution')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
