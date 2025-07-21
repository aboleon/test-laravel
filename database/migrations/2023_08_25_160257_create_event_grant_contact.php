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
        Schema::create('event_grant_contact', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grant_id')->constrained('event_grant')->onUpdate('no action')->onDelete('cascade');
            $table->text('first_name');
            $table->text('last_name');
            $table->string('email');
            $table->string('phone');
            $table->text('fonction')->nullable();
            $table->text('service')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_contact');
    }
};
