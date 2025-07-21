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
        Schema::create('event_contact_sellable_service_choosables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_contact_id')->constrained('events_contacts', indexName: 'event_contact_sellable_service_choosables_contact_id_foreign')->cascadeOnDelete();
            $table->foreignId('choosable_id')->constrained('event_sellable_service')->cascadeOnDelete();
            $table->enum('status', ApprovalResponseStatus::keys())->default(ApprovalResponseStatus::default())->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_contact_sellable_service_choosables');
    }
};
