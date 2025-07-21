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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'comment')) {
                $table->string('comment')->nullable();
                $table->unsignedInteger('price_after_tax');
                $table->unsignedInteger('price_before_tax');
                $table->foreignId("vat_id")->nullable()->constrained("vat")->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('comment');
            $table->dropColumn('price_after_tax');
            $table->dropColumn('price_before_tax');
            $table->dropForeign('orders_vat_id_foreign');
            $table->dropColumn('vat_id');
        });
    }
};
