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
            $table->foreignId('service_group_combined')->nullable()->after('service_group')->references('id')->on('dictionnary_entries')->nullOnDelete()->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sellable_service', function (Blueprint $table) {
            $table->dropForeign('event_sellable_service_service_group_combined_foreign');
            $table->dropColumn('service_group_combined');
        });
    }
};
