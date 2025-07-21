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
        Schema::create('event_program_session_moderators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('events_contacts_id')
                ->constrained('events_contacts', indexName: 'epsm_events_contacts_id')
                ->onDelete('CASCADE');

            $table->foreignId('event_program_session_id')
                ->constrained('event_program_sessions', indexName: 'epsm_eps_id')
                ->onDelete('CASCADE');

            $table->foreignId('moderator_type_id')
                ->nullable()
                ->constrained('dictionnary_entries', indexName: 'epsm_dic_id')
                ->references('id')
                ->on('dictionnary_entries')->restrictOnDelete();
            $table->boolean('allow_video_distribution')->nullable();
            $table->enum('status', EventProgramParticipantStatus::keys())->default(EventProgramParticipantStatus::default());

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_program_session_moderators');
    }
};
