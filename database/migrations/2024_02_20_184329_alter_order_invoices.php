<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use \MetaFramework\Traits\MetaSchema;

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement('SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0');

        Schema::dropIfExists('order_invoices');

        Schema::create('order_invoices', function (Blueprint $table) {

            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string("invoice_number")->nullable();
            $table->timestamp("created_at")->useCurrent();
            $table->timestamp("cancelled_at")->nullable();
        });

        DB::statement('SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
