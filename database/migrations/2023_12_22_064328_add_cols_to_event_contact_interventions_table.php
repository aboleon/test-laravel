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
        Schema::table('events_contacts_interventions', function (Blueprint $table) {
            if (!Schema::hasColumn('events_contacts_interventions', 'allow_pdf_distribution')) {
                $table->boolean('allow_pdf_distribution')->nullable();
            }
            if (!Schema::hasColumn('events_contacts_interventions', 'allow_video_distribution')) {
                $table->boolean('allow_video_distribution')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_contacts_interventions', function (Blueprint $table) {
            if (Schema::hasColumn('events_contacts_interventions', 'allow_pdf_distribution')) {
                $table->dropColumn('allow_pdf_distribution');
            }
            if (Schema::hasColumn('events_contacts_interventions', 'allow_video_distribution')) {
                $table->dropColumn('allow_video_distribution');
            }
        });
    }
};
