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
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('has_program')
                ->default(null)
                ->change();
            $table->boolean('transferts_participants')->nullable()->after('has_transferts');
            $table->renameColumn('has_transferts', 'transferts_speakers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('transferts_speakers', 'has_transferts');
            $table->dropColumn('transferts_participants');
            $table->boolean('has_program')
                ->default(0)
                ->change();
        });
    }
};
