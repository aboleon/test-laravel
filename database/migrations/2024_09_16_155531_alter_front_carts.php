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
        Schema::table('front_carts', function (Blueprint $table) {
           $table->foreignId('order_id')->after('event_contact_id')->nullable()->constrained('orders');
           $table->boolean('is_group_order')->nullable();
           $table->boolean('pec_eligible')->nullable();
           $table->longText('pec')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_carts', function (Blueprint $table) {
            $table->dropColumn('is_group_order');
            $table->dropForeign('front_carts_order_id_foreign');
            $table->dropColumn('order_id');
            $table->dropColumn('pec_eligible');
            $table->dropColumn('pec');
        });
    }
};
