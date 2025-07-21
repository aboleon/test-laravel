<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_grant_deposit_participation', function (Blueprint $table) {

            $table->foreignId('deposit_id');
            $table->foreign('deposit_id', 'fk_event_grant_deposit_participation')
                ->references('id')
                ->on('event_grant_deposit')
                ->cascadeOnDelete();

            $table->foreignId('participation_id');
            $table->foreign('participation_id', 'fk_essgbpid')->references('id')->on('participation_types')->cascadeOnDelete();
            $table->string('continent')->nullable();
            $table->primary(['deposit_id', 'participation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_deposit_participation');
    }
};
