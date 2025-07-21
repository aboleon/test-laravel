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
        Schema::dropIfExists('event_grant_allocations');

        Schema::create('event_grant_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grant_id')->constrained('event_grant')->onDelete('cascade');
            $table->integer('nb_allocated')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_allocations');
    }
};
