<?php

use App\Enum\EventDepositStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('order_sellable_deposits');

        Schema::create('order_sellable_deposits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('shoppable_type');
            $table->unsignedBigInteger('shoppable_id');
            $table->unsignedBigInteger('vat_id')->nullable();
            $table->unsignedBigInteger('event_contact_id');

            $table->string("shoppable_label");
            $table->string("total_net");
            $table->string("total_vat");
            $table->enum('status', EventDepositStatus::keys())->nullable()->index();
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
