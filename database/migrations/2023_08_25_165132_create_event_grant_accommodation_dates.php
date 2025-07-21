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
        Schema::create('event_grant_accommodation_dates', function (Blueprint $table) {
            $table->foreignId('grant_id');
            $table->foreign('grant_id', 'FK_event_grant_accommodation_dates_event_grant')
                ->references('id')
                ->on('event_grant')
                ->cascadeOnDelete();
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_accommodation_dates');
    }
};
