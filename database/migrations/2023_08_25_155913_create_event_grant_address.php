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
        Schema::create('event_grant_address', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grant_id')->constrained('event_grant')->onUpdate('no action')->onDelete('cascade');
            $table->string('street_number')->nullable();
            $table->string('route')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('locality')->nullable();
            $table->string('country_code',2)->nullable();
            $table->string('administrative_area_level_1')->nullable();
            $table->string('administrative_area_level_2')->nullable();
            $table->text('text_address')->nullable();
            $table->decimal('lat', 16, 13, true)->nullable();
            $table->decimal('lon', 16, 13, true)->nullable();
            $table->text('complementary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_address');
    }
};
