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
        Schema::create('event_accommodation_blocked_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_accommodation_id')->constrained('event_accommodation')->cascadeOnDelete();
            $table->string('group_id')->index();
            $table->foreignId('participation_type')->nullable()->references('id')->on('participation_types')->restrictOnDelete();
            $table->enum('participation_group', ParticipantType::keys())->nullable()->index();
            $table->date('date')->index();
            $table->foreignId('room_group_id')->constrained('event_accommodation_room_groups')->cascadeOnDelete();
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('grant')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_accommodation_blocked_room');
    }
};
