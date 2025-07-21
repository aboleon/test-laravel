<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('front_cart_lines', function ($table) {
            $table->id();
            $table->foreignId('front_cart_id')->constrained('front_carts')->cascadeOnDelete();

            $table->uuid()->index();
            $table->morphs('shoppable');
            $table->unsignedMediumInteger('quantity');
            $table->unsignedInteger('total_net')->default(0);
            $table->unsignedInteger('total_ttc')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('front_cart_lines');
    }
};
