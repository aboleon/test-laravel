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
        Schema::table('event_grant_location', function (Blueprint $table) {
            $table->enum('type', ['locality','country','continent'])->default('country')->after('grant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_grant_location', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
