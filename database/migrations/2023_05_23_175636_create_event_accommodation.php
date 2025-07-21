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
        Schema::create('event_accommodation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('vat_id')->nullable()->constrained('vat')->cascadeOnDelete();
            $table->longText('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('cancellation')->nullable();
            $table->boolean('pec')->nullable()->default(1);
            $table->timestamp('published')->nullable();
            $table->longText('participation_types')->nullable();
            $table->unsignedInteger('total_commission')->default(0);
            $table->unsignedInteger('turnover')->default(0);
            $table->unsignedInteger('total_cancellation')->default(0);
            $table->unsignedInteger('processing_fee')->default(0);
            $table->timestamps();
            $table->unique(['hotel_id','event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_accommodation');
    }
};
