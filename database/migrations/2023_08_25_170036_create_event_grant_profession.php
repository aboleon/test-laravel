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
        Schema::create('event_grant_profession', function (Blueprint $table) {
            $table->foreignId('grant_id');
            $table->foreign('grant_id', 'fk_event_grant_profession')
                ->references('id')
                ->on('event_grant')->cascadeOnDelete();

            $table->foreignId('profession_id');
            $table->foreign('profession_id', 'fk_event_grant_profession_id')->references('id')->on('dictionnary_entries')->cascadeOnDelete();

            $table->unsignedInteger('pax')->nullable();

            $table->primary(['grant_id', 'profession_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_profession');
    }
};
