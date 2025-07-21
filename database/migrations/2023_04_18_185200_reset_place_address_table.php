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
        Schema::dropIfExists('place_addresses');
        Schema::create('place_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->decimal('lat', 16, 13, true)->nullable()->index();
            $table->decimal('lon', 16, 13, true)->nullable()->index();
            $table->string('postal_code')->nullable()->index();
            $table->string('country_code')->nullable()->index();
            $table->string('street_number')->nullable();
            $table->string('route')->nullable();
            $table->string('locality')->nullable();
            $table->string('administrative_area_level_1')->nullable();
            $table->string('administrative_area_level_1_short')->nullable();
            $table->string('administrative_area_level_2')->nullable();
            $table->text('text_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_addresses');
    }
};
