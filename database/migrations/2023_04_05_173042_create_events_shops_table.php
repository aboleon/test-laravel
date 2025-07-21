<?php

use App\Enum\EventShoppingMode;
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
        Schema::create('events_shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->boolean('is_active')->nullable()->default(1);
            $table->foreignId('admin_id')->nullable()->references('id')->on('users')->restrictOnDelete();
            $table->timestamp('shopping_limit_date')->nullable();
            $table->foreignId('vat_id')->nullable()->references('id')->on('vat')->restrictOnDelete();
            $table->enum('shopping_mode', EventShoppingMode::keys())->default(EventShoppingMode::default());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_shops');
    }
};
