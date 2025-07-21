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
        Schema::create('event_transports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('events_contacts_id')->constrained('events_contacts')->cascadeOnDelete();

            $table->boolean('departure_online')->nullable();
            $table->foreignId('departure_step')->nullable()->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('departure_transport_type')->nullable()->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->date('departure_start_date')->nullable();
            $table->time('departure_start_time')->nullable();
            $table->string('departure_start_location')->nullable();
            $table->time('departure_end_time')->nullable();
            $table->string('departure_end_location')->nullable();
            $table->text('departure_reference_info_participant')->nullable();
            $table->text('departure_participant_comment')->nullable();


            $table->boolean('return_online')->nullable();
            $table->foreignId('return_step')->nullable()->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('return_transport_type')->nullable()->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->date('return_start_date')->nullable();
            $table->time('return_start_time')->nullable();
            $table->string('return_start_location')->nullable();
            $table->time('return_end_time')->nullable();
            $table->string('return_end_location')->nullable();
            $table->text('return_reference_info_participant')->nullable();
            $table->text('return_participant_comment')->nullable();

            $table->string('transfer_shuttle_time')->nullable();
            $table->text('transfer_info')->nullable();
            $table->text('travel_preferences')->nullable();


            $table->unsignedInteger('price_before_tax')->nullable();
            $table->unsignedInteger('price_after_tax')->nullable();
            $table->unsignedInteger('max_reimbursement')->nullable();
            $table->text('admin_comment')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_transports');
    }
};
