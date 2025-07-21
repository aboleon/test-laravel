<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('events_clients')) {
            Schema::dropIfExists('events_clients');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
