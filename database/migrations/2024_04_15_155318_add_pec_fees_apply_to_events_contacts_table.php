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
        Schema::table('events_contacts', function (Blueprint $table) {
            $table->boolean('pec_fees_apply')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_contacts', function (Blueprint $table) {
            $table->dropColumn('pec_fees_apply');
        });
    }
};
