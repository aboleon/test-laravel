<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MetaFramework\Accessors\VatAccessor;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_preorders', function (Blueprint $table) {
            $table->dropColumn('grant_allocations');
            $table->dropColumn('grant_id');
            $table->boolean('discharged_from_payment')->default(false);
            $table->boolean('is_pec_eligible')->default(false);
            $table->longText('pec')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_preorders', function (Blueprint $table) {
            $table->longText('grant_allocations')->nullable();
            $table->unsignedInteger('grant_id')->nullable();
            $table->dropColumn('discharged_from_payment');
            $table->dropColumn('is_pec_eligible');
            $table->dropColumn('pec');

        });
    }
};
