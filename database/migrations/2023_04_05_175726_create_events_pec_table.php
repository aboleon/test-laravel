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
        Schema::create('events_pec', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->boolean('is_active')->nullable()->default(1);
            $table->foreignId('admin_id')->nullable()->references('id')->on('users')->restrictOnDelete();
            $table->foreignId('grant_admin_id')->nullable()->references('id')->on('users')->restrictOnDelete();
            $table->unsignedInteger('processing_fees')->nullable();
            $table->unsignedInteger('waiver_fees')->nullable();
            $table->foreignId('processing_fees_vat_id')->nullable()->references('id')->on('vat')->restrictOnDelete();
            $table->foreignId('waiver_fees_vat_id')->nullable()->references('id')->on('vat')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_pec');
    }
};
