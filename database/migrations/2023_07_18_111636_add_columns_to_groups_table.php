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
        Schema::table('groups', function (Blueprint $table) {
            $table->string('siret', 14)->nullable();
            $table->string('vat_id', 20)->nullable();
            if (Schema::hasColumn('groups', 'default_billing_address')) {
                $table->dropForeign('groups_default_billing_address_foreign');
                $table->dropColumn('default_billing_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('siret');
            $table->dropColumn('vat_id');
        });
    }
};
