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
        Schema::create('sellables', function (Blueprint $table) {
            $table->id();
            $table->timestamp('deleted_at')->nullable()->index();
            $table->boolean('published')->nullable()->index();
            $table->string('sku')->nullable()->index();
            $table->foreignId('category_id')->references('id')->on('dictionnary_entries')->onUpdate('no action')->restrictOnDelete();
            $table->foreignId('vat_id')->constrained('vat')->onUpdate('no action')->restrictOnDelete();
            $table->unsignedInteger('price')->index();
            $table->unsignedInteger('price_buy')->index();
            $table->longText('title')->nullable();
            $table->longText('description')->nullable();
            $table->enum('sold_per', SellablePer::keys())->default(SellablePer::default());
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellables');
    }
};
