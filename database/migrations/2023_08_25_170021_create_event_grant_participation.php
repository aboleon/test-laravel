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
        Schema::create('event_grant_participation', function (Blueprint $table) {

            $table->foreignId('grant_id');
            $table->foreign('grant_id', 'fk_event_grant_participation')
                ->references('id')
                ->on('event_grant')
                ->cascadeOnDelete();

            $table->foreignId('participation_id');
            $table->foreign('participation_id', 'fk_event_grant_participation_id')->references('id')->on('participation_types')->cascadeOnDelete();

            $table->unsignedInteger('pax')->nullable();


            $table->primary(['grant_id', 'participation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_participation');
    }
};
