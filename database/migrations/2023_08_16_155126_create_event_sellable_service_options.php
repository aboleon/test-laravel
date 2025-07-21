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
        Schema::create('event_sellable_service_options', function (Blueprint $table) {

            $table->id();
            $table->foreignId('event_sellable_service_id');
            $table->foreign('event_sellable_service_id', 'fk_option_essid')
                ->references('id')
                ->on('event_sellable_service')
                ->cascadeOnDelete();
            $table->longText('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sellable_service_options');
    }
};
