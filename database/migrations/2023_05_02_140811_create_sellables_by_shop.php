<?php

use App\Enum\SellablePer;
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
        Schema::create('sellables_by_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onUpdate('no action')->cascadeOnDelete();
            $table->foreignId('sellable_id')->constrained('sellables')->onUpdate('no action')->cascadeOnDelete();
            $table->foreignId('vat_id')->constrained('vat')->onUpdate('no action')->restrictOnDelete();
            $table->string('sku')->nullable()->index();
            $table->unsignedInteger('price')->index();
            $table->unsignedInteger('price_buy')->index();
            $table->longText('title')->nullable();
            $table->longText('description')->nullable();
            $table->enum('sold_per', SellablePer::keys())->default(SellablePer::default());
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable()->index();

            $table->unique(['event_id','sellable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellables_by_event');
    }
};
