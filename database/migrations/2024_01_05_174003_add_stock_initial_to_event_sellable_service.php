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
        Schema::table('event_sellable_service', function (Blueprint $table) {
            $table->unsignedInteger('stock_initial')->default(0)->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sellable_service', function (Blueprint $table) {
            $table->dropColumn('stock_initial');
        });
    }
};
