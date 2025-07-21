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
        Schema::table('event_sellable_service_deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('event_sellable_service_deposits', 'vat_id')) {
                $table->foreignId('vat_id')->nullable()->after('amount')->references('id')->on('vat')->restrictOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sellable_service_deposits', function (Blueprint $table) {
            if (Schema::hasColumn('event_sellable_service_deposits', 'vat_id')) {
                $table->dropForeign(['vat_id']);
                $table->dropColumn('vat_id');
            }
        });
    }
};
