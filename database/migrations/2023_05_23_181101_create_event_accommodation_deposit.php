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
        Schema::create('event_accommodation_deposit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_accommodation_id')->constrained('event_accommodation')->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->date('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_accommodation_deposit');
    }
};
