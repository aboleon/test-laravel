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
        Schema::table('account_phones', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('country_code',2)->after('user_id')->default('FR')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_phones', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('country_code');
            $table->dropTimestamps();
        });
    }
};
