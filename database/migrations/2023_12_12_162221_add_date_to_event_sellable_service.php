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
            $table->date('service_date')->nullable()->after('place_id');
            $table->time('service_starts')->nullable()->after('service_date');
            $table->time('service_ends')->nullable()->after('service_starts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sellable_service', function (Blueprint $table) {
            $table->dropColumn('service_date');
            $table->dropColumn('service_starts');
            $table->dropColumn('service_ends');
        });
    }
};
