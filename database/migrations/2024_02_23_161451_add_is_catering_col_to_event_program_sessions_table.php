<?php

use App\Enum\OrderOrigin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_program_sessions', function (Blueprint $table) {
            if (false === Schema::hasColumn('event_program_sessions', 'is_catering')) {
                $table->boolean('is_catering')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('event_program_sessions', 'is_catering')) {
                $table->dropColumn('is_catering');
            }
        });
    }
};
