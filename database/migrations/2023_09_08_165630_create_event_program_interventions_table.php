<?php

use App\Enum\EventProgramParticipantStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('event_program_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_program_session_id')->constrained('event_program_sessions')->cascadeOnDelete();

            $table->integer('position')->default(0);

            $table->boolean('is_online')->nullable();
            $table->boolean('is_visible_in_front')->nullable();


            $table->foreignId('place_room_id')->constrained()->onUpdate('no action')->onDelete('cascade');

            $table->foreignId('group_id')->nullable()->constrained()->onUpdate('no action')->onDelete('cascade');

            $table->longText('name')->nullable();
            $table->longText('description')->nullable();

            $table->longText('internal_comment')->nullable();



            $table->enum('status', EventProgramParticipantStatus::keys())->default(EventProgramParticipantStatus::default());


            $table->foreignId('specificity_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();

            $table->boolean('allow_pdf_distribution')->nullable();
            $table->boolean('allow_video_distribution')->nullable();

            $table->integer('duration')->default(0);
            $table->time('preferred_start_time')->nullable();
            $table->longText('intervention_timing_details')->nullable();

            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_program_interventions');
    }
};
