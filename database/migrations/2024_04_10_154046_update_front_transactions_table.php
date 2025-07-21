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
        Schema::table('front_transactions', function (Blueprint $table) {
            $table->string('num_appel')->after('num_trans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
