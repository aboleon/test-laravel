<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_sellable_service_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_sellable_service_id');
            $table->foreign('event_sellable_service_id', 'fk_deposit_essid')
                ->references('id')
                ->on('event_sellable_service')
                ->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sellable_service_deposits');
    }
};
