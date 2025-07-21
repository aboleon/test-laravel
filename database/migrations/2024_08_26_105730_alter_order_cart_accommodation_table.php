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
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            if (Schema::hasColumn('order_cart_accommodation', 'xyz')) {
                $table->dropColumn('xyz');
            }
            $table->foreignId('amended_cart_id')->nullable()->constrained('order_cart_accommodation')->nullOnDelete();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `order_cart_accommodation` DROP FOREIGN KEY `order_cart_accommodation_amended_cart_id_foreign`');
        DB::statement('ALTER TABLE `order_cart_accommodation` DROP COLUMN `amended_cart_id`');
    }
};
