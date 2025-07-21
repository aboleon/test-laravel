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
        DB::statement('DELETE FROM order_cart_accommodation_attributions');

        Schema::table('order_cart_accommodation_attributions', function (Blueprint $table) {
            $table->dropForeign('order_cart_accommodation_attributions_cart_id_foreign');
            $table->dropColumn('cart_id');
            $table->dropForeign('order_cart_accommodation_attributions_room_id_foreign');
            $table->dropColumn('room_id');
        });

        Schema::table('order_cart_accommodation_attributions', function (Blueprint $table) {
            $table->foreignId('cart_id')->after('event_contact_id')->constrained('order_cart_accommodation')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DELETE FROM order_cart_accommodation_attributions');

        Schema::table('order_cart_accommodation_attributions', function (Blueprint $table) {
            $table->dropForeign('order_cart_accommodation_attributions_cart_id_foreign');
            $table->dropColumn('cart_id');
            $table->dropColumn('created_at');
        });


        Schema::table('order_cart_accommodation_attributions', function (Blueprint $table) {
            $table->foreignId('cart_id')->constrained('order_cart')->restrictOnDelete();
            $table->foreignId('room_id')->constrained('event_accommodation_room')->restrictOnDelete();
        });
    }
};
