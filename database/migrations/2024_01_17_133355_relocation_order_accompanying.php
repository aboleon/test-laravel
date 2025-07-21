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
            $table->removeColumn('quantity_accompagnying');
            $table->removeColumn('accompagnying');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            $table->unsignedInteger('quantity_accompagnying')->default(0);
            $table->text('accompagnying')->nullable();
        });
    }
};
