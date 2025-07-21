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
        Schema::table('order_cart', function (Blueprint $table) {
            if (Schema::hasColumn('order_cart', 'shopable_type')) {
                $table->renameColumn('shopable_type', 'shoppable_type');
                $table->renameColumn('shopable_id', 'shoppable_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart', function (Blueprint $table) {
            $table->renameColumn('shoppable_type', 'shopable_type');
            $table->renameColumn('shoppable_id', 'shopable_id');
        });
    }
};
