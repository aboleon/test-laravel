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
        if (Schema::hasColumn('account_profile', 'billing_address')) {
            Schema::table('account_profile', function (Blueprint $table) {
                $table->dropForeign('account_profile_billing_address_foreign');
                $table->dropColumn('billing_address');
            });
        }
        Schema::table('account_address', function (Blueprint $table) {
            $table->boolean('billing')->after('user_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_address', function (Blueprint $table) {
            $table->dropColumn('billing');
        });
    }
};
