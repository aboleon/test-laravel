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
        Schema::create('event_sellable_service_choosable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id');
            $table->foreign('event_id', 'fk_choosable_event_id')
                ->references('id')
                ->on('events')
                ->cascadeOnDelete();
            $table->boolean('published')->nullable()->index();
            $table->longText('title');
            $table->longText('description');
            $table->longText('description_public')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sellable_service_choosable');
    }
};
