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
        Schema::table('event_grant', function (Blueprint $table) {
            $table->char("amount_type", 3)->nullable()->after('amount_ttc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_grant', function (Blueprint $table) {
            $table->dropColumn('amount_type');
        });
    }
};
