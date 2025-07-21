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
        Schema::create('event_grant_deposit_location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_id');
            $table->foreign('deposit_id', 'fk_grant_location_deposit_id')
                ->references('id')
                ->on('event_grant_deposit')
                ->cascadeOnDelete();
            $table->string('locality')->nullable();
            $table->string('country_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_deposit_location');
    }
};
