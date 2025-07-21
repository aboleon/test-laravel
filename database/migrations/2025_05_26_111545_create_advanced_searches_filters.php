<?php

use App\Enum\SavedSearches;
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
        Schema::create('advanced_searches_filters', function (Blueprint $table) {
            $table->foreignId('auth_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', SavedSearches::values())->default(null);
            $table->longText('filters');

            $table->primary(['auth_id', 'type']);

            $table->index(['auth_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advanced_searches_filters');
    }
};
