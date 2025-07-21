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
        Schema::create('advanced_searches', function (Blueprint $table) {
            $table->foreignId('auth_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->unsignedBigInteger('id'); // This will store the result ID

            // Create a primary key from these columns
            $table->primary(['auth_id', 'type', 'id']);

            // Add index for faster joins
            $table->index('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advanced_searches');
    }
};
