<?php

use App\Enum\OrderCartType;
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

        Schema::dropIfExists('order_service_attributions');
        Schema::dropIfExists('order_cart_accommodation_attributions');

        Schema::create('order_attributions', function (Blueprint $table) {
            $table->id();
            $table->enum('shoppable_type', [OrderCartType::SERVICE->value, OrderCartType::ACCOMMODATION->value])->index();
            $table->unsignedInteger('shoppable_id');
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->foreignId('event_contact_id')->constrained('events_contacts')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->longText('attributes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_attributions');
    }
};
