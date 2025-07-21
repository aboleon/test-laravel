<?php

use App\Enum\ApprovalResponseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_contact_sellable_service_choosables', function (Blueprint $table) {
            if (!Schema::hasColumn('event_contact_sellable_service_choosables', 'invitation_quantity_accepted')) {
                $table->boolean("invitation_quantity_accepted")->nullable();
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_contact_sellable_service_choosables', function (Blueprint $table) {
            if (Schema::hasColumn('event_contact_sellable_service_choosables', 'invitation_quantity_accepted')) {
                $table->dropColumn('invitation_quantity_accepted');
            }
        });
    }
};
