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
        Schema::create('event_accommodation_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_accommodation_id')->constrained('event_accommodation')->cascadeOnDelete();
            $table->foreignId('vat_id')->constrained('vat')->cascadeOnDelete();
            $table->longText('name');
            $table->unsignedInteger('price');
            $table->longText('participation_types')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_accommodation_service');
    }
};
