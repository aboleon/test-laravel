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
            $table->foreignId('place_room_id')->constrained()->onUpdate('no action')->onDelete('cascade');
            $table->longText('name')->nullable();
            $table->integer('duration')->default(0);
            $table->time('preferred_start_time')->nullable();

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
