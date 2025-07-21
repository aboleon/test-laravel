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
        Schema::create('event_sellable_service_participation', function (Blueprint $table) {

            $table->foreignId('event_sellable_service_id');
            $table->foreign('event_sellable_service_id', 'fk_ptype_essid')
                ->references('id')->on('event_sellable_service')->cascadeOnDelete();

            $table->foreignId('participation_id');
            $table->foreign('participation_id', 'fk_event_sellable_participation_id')->references('id')->on('participation_types')->cascadeOnDelete();

            $table->primary(['event_sellable_service_id', 'participation_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sellable_service_participation');
    }
};
