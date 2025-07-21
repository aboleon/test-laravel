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
        Schema::create('event_sellable_service_profession', function (Blueprint $table) {
            $table->foreignId('event_sellable_service_id');
            $table->foreign('event_sellable_service_id', 'fk_prof_essid')
                ->references('id')->on('event_sellable_service')->cascadeOnDelete();

            $table->foreignId('profession_id');
            $table->foreign('profession_id', 'fk_event_sellable_profession_id')->references('id')->on('dictionnary_entries')->cascadeOnDelete();

            $table->primary(['event_sellable_service_id', 'profession_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sellable_service_profession');
    }
};
