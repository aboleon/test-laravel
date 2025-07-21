<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_sellable_service', function (Blueprint $table) {
            if (!Schema::hasColumn('event_sellable_service', 'virtual_stock')) {
                $table->unsignedInteger('virtual_stock')->default(0)->after("stock");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sellable_service', function (Blueprint $table) {
            if (Schema::hasColumn('event_sellable_service', 'virtual_stock')) {
                $table->dropColumn('virtual_stock');
            }
        });
    }
};
