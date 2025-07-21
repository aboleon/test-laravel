<?php

use App\Services\Pec\PecType;
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
        Schema::create('pec_distribution', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grant_id')->constrained('event_grant')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('event_contact_id')->constrained('events_contacts')->cascadeOnDelete();
            $table->enum('type', PecType::keys())->default(PecType::default());
            $table->unsignedInteger('amount');
            $table->binary('service_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pec_distribution');
    }
};
