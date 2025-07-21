<?php

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
        Schema::create('events_contacts_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('events_contacts_id')
                ->constrained('events_contacts', indexName: 'eci_events_contacts_id')
                ->onDelete('CASCADE');

            $table->foreignId('event_program_intervention_id')
                ->constrained('event_program_interventions', indexName: 'eci_epi_id')
                ->onDelete('CASCADE');


            $table->unique(['events_contacts_id', 'event_program_intervention_id'], 'eci_to_epi_unique');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_contacts_interventions');
    }
};
