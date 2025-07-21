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
        Schema::table('pec_distribution', function (Blueprint $table) {
            $table->renameColumn('amount', 'unit_price');
            $table->renameColumn('sub_vat', 'total_vat');
            $table->renameColumn('sub_ht', 'total_net');
            $table->dropColumn('service_id');
        });

        Schema::table('pec_distribution', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->after('unit_price');
            $table->unsignedInteger('vat_id')->default(\MetaFramework\Accessors\VatAccessor::defaultRate()->id)->after('total_vat');
            $table->unsignedInteger('sellable_id')->after('vat_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pec_distribution', function (Blueprint $table) {
            $table->renameColumn('unit_price', 'amount');
            $table->dropColumn('quantity');
            $table->renameColumn('total_net', 'sub_ht');
            $table->renameColumn('total_vat', 'sub_vat');
            $table->dropColumn('vat_id');
            $table->dropColumn('sellable_id');
        });
        Schema::table('pec_distribution', function (Blueprint $table) {
            $table->binary('service_id')->after('sub_vat')->nullable();
        });
    }
};
