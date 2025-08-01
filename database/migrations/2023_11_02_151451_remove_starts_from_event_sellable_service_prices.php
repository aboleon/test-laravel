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
        Schema::table('event_sellable_service_prices', function (Blueprint $table) {
            $table->dropColumn('starts');
            $table->date('ends')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sellable_service_prices', function (Blueprint $table) {
            $table->timestamp('starts')->nullable();
            $table->timestamp('ends')->change();
        });
    }
};
