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
        Schema::create('event_grant_location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grant_id');
            $table->foreign('grant_id', 'fk_event_grant_location')
                ->references('id')
                ->on('event_grant')
                ->cascadeOnDelete();
            $table->string('locality')->nullable();
            $table->string('country_code')->nullable();
            $table->unsignedInteger('pax')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_location');
    }
};
