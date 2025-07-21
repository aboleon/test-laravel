<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_sellable_deposits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained();

            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('vat_id');
            $table->unsignedBigInteger('event_contact_id');

            $table->string("service_name");
            $table->string("total_net");
            $table->string("total_vat");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_sellable_deposits');
    }
};
