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
        Schema::table('events_pec', function (Blueprint $table) {
            if (!Schema::hasColumn('events_pec', 'waiver_fees_vat_id')) {
                $table->foreignId('waiver_fees_vat_id')->nullable()->references('id')->on('vat')->restrictOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_pec', function (Blueprint $table) {
            if (Schema::hasColumn('events_pec', 'waiver_fees_vat_id')) {
                $table->dropForeign(['waiver_fees_vat_id']);
                $table->dropColumn('waiver_fees_vat_id');
            }
        });
    }
};
