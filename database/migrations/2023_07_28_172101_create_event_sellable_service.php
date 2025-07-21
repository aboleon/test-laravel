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
        Schema::create('event_sellable_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onUpdate('no action')->cascadeOnDelete();
            $table->boolean('published')->nullable()->index();
            $table->foreignId('service_group')->nullable()->references('id')->on('dictionnary_entries')->nullOnDelete()->onUpdate('no action');
            $table->foreignId('place_id')->nullable()->references('id')->on('places')->nullOnDelete()->onUpdate('no action');
            $table->unsignedInteger('stock');
            $table->boolean('stock_unlimited')->nullable();
            $table->unsignedInteger('stock_showable')->nullable();
            $table->boolean('pec_eligible')->nullable()->default(1);
            $table->unsignedInteger('pec_max_pax')->default(1);
            $table->boolean('limited_to_one')->nullable();
            $table->foreignId('vat_id')->nullable()->constrained('vat')->onUpdate('no action')->noActionOnDelete();
            $table->longText('title');
            $table->longText('description');
            $table->longText('description_public')->nullable();
            $table->longText('vat_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('event_sellable_service');
        Schema::enableForeignKeyConstraints();
    }
};
