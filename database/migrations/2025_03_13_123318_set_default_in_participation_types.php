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
        Schema::table('participation_types', function (Blueprint $table) {
            $table->boolean('default')->default(false);
        });

        DB::table('participation_types')->where('id', 4)->update(['default' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participation_types', function (Blueprint $table) {
            $table->dropColumn('default');
        });
    }
};
