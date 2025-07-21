<?php

use App\Enum\ParticipantType;
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

        Schema::table('event_accommodation_blocked_room', function (Blueprint $table) {
            $table->dropForeign('event_accommodation_blocked_room_participation_type_foreign');
            $table->dropColumn('participation_type');
            $table->dropColumn('participation_group');
        });

        Schema::table('event_accommodation_blocked_room', function (Blueprint $table) {
            $table->text('participation_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {


        Schema::table('event_accommodation_blocked_room', function (Blueprint $table) {
            $table->dropColumn('participation_type');
        });

        Schema::table('event_accommodation_blocked_room', function (Blueprint $table) {
            $table->foreignId('participation_type')->nullable()->references('id')->on('participation_types')->restrictOnDelete();
            $table->enum('participation_group', ParticipantType::keys())->nullable()->index();
        });
    }
};
