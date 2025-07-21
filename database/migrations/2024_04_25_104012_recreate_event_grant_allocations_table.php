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
        Schema::dropIfExists('event_grant_allocations');

        Schema::create('event_grant_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grant_id')->constrained('event_grant')->onDelete('cascade');
            $table->foreignId('event_contact_id')->constrained('events_contacts')->onDelete('cascade');

            // Morph relationship columns
            $table->unsignedBigInteger('eligibility_id')->nullable();
            $table->string('eligibility_type')->nullable();


            $table->string('locality')->nullable();
            $table->string('country_code')->nullable();
            $table->string('continent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_allocations');
    }
};
