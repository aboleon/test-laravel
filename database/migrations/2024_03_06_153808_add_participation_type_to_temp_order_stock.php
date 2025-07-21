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
        Schema::table('order_temp_stock', function (Blueprint $table) {
            $table->unsignedInteger('participation_type_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_temp_stock', function (Blueprint $table) {
            $table->dropColumn('participation_type_id');
        });
    }
};
