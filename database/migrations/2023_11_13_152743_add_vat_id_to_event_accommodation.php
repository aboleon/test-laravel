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
        Schema::table('event_accommodation', function (Blueprint $table) {
            $table->foreignId('processing_fee_vat_id')->after('processing_fee')->nullable()->constrained('vat')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_accommodation', function (Blueprint $table) {
            $table->dropForeign('event_accommodation_processing_fee_vat_id_foreign');
            $table->dropColumn('processing_fee_vat_id');
        });
    }
};
