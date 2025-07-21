<?php

use App\Enum\EventProgramParticipantStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_program_session_moderators', function (Blueprint $table) {
            $table->dropColumn("status");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_program_session_moderators', function (Blueprint $table) {
            $table->enum('status', EventProgramParticipantStatus::keys())->default(EventProgramParticipantStatus::default());
        });
    }
};
