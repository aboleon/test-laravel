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
        Schema::create('events_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->longText('name');
            $table->longText('subname')->nullable();
            $table->longText('description')->nullable();
            $table->longText('cancelation')->nullable();
            $table->longText('external_accommodation')->nullable();
            $table->longText('cancellation_shop')->nullable();
            $table->longText('transport_admin')->nullable();
            $table->longText('transport_user')->nullable();
            $table->longText('transport_unnecessary')->nullable();
            $table->longText('contact_title')->nullable();
            $table->longText('contact_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_texts');
    }
};
