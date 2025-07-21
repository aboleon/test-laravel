<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('front_payment_calls', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->unsignedInteger('provider')->index();
            $table->foreignId('cart_id')->constrained('front_carts')->cascadeOnDelete();
            $table->unsignedInteger('total');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('front_payment_calls');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
