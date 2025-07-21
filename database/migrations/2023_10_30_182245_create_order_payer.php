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
        Schema::create('order_payer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('company')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('department')->nullable();
            $table->string('adresse_line_1');
            $table->string('adresse_line_2')->nullable();
            $table->string('adresse_line_3')->nullable();
            $table->string('zip');
            $table->string('locality');
            $table->string('cedex')->nullable();
            $table->string('country_code');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payer');
    }
};
