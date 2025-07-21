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
        Schema::table('events_texts', function (Blueprint $table) {
            if (false === Schema::hasColumn('events_texts', 'max_price_text')) {
                $table->longText('max_price_text')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_texts', function (Blueprint $table) {
            if (true === Schema::hasColumn('events_texts', 'max_price_text')) {
                $table->dropColumn('max_price_text');
            }
        });
    }
};
