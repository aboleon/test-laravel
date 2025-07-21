<?php

use App\Enum\DesiredTransportManagement;
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
        Schema::create('events_contacts_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('events_contacts_id')->constrained('events_contacts')->unique()->cascadeOnDelete();
            $table->enum('transport_desired_management', DesiredTransportManagement::keys())->nullable()->index();
            $table->boolean('transport_transfer_requested')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_contacts_transports');
    }
};
