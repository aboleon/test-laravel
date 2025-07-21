<?php

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
        Schema::create('event_grant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->boolean('active')->nullable();
            $table->longText('title');
            $table->unsignedInteger('amount_ht');
            $table->unsignedInteger('amount_ttc');
            $table->unsignedInteger('pax_min');
            $table->unsignedInteger('pax_avg');
            $table->unsignedInteger('pax_max');
            $table->unsignedInteger('pec_fee');
            $table->unsignedInteger('deposit_fee');
            $table->boolean('manage_transport_upfront')->nullable();
            $table->boolean('manage_transfert_upfront')->nullable();
            $table->boolean('refund_transport')->nullable();
            $table->unsignedInteger('refund_transport_amount')->nullable();
            $table->unsignedInteger('age_eligible_min')->nullable();
            $table->unsignedInteger('age_eligible_max')->nullable();
            $table->longText('refund_transport_text')->nullable();
            $table->text('comment')->nullable();
            $table->date('prenotification_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant');
    }
};
