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
        Schema::table('payment_call', function (Blueprint $table) {
            $table->unsignedBigInteger('group_manager_id')->after('provider')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_call', function (Blueprint $table) {
            $table->dropColumn('group_manager_id');
        });
    }
};
