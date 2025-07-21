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
        Schema::table('order_cart_service', function (Blueprint $table) {

            if (Schema::hasColumn('order_cart_service', 'date')) {

                $table->dropColumn('date');
                $table->dropForeign('service_cart_service_id_foreign');
                $table->dropColumn('service_id');

                            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
