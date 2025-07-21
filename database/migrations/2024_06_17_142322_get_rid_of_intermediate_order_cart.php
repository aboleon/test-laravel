<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MetaFramework\Traits\MetaSchema;

return new class extends Migration {

    use MetaSchema;

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // order_cart_accommodation
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->nullable()->constrained('orders')->cascadeOnDelete();
        });

        DB::statement('
            UPDATE order_cart_accommodation AS oca
            JOIN order_cart AS oc ON oca.cart_id = oc.id
            SET oca.order_id = oc.order_id
        ');

        Schema::table('order_cart_accommodation', function (Blueprint $table) {

            if ($this->hasForeignKey($table, 'accommodation_cart_cart_id_foreign')) {
                $table->dropForeign('accommodation_cart_cart_id_foreign');
            }
            if ($this->hasForeignKey($table, 'order_cart_accommodation_cart_id_foreign')) {
                $table->dropForeign('order_cart_accommodation_cart_id_foreign');
            }

            $table->dropColumn('cart_id');
            $table->foreignId('order_id')->nullable(false)->change();
        });

        // ------------------------------------------




        // order_cart_accommodation_attributions

        Schema::table('order_cart_accommodation_attributions', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->nullable()->constrained('orders')->cascadeOnDelete();
        });

        DB::statement('
            UPDATE order_cart_accommodation_attributions AS oca
            JOIN order_cart AS oc ON oca.cart_id = oc.id
            SET oca.order_id = oc.order_id
        ');

        Schema::table('order_cart_accommodation_attributions', function (Blueprint $table) {
            $table->dropForeign('order_cart_accommodation_attributions_cart_id_foreign');
            $table->dropColumn('cart_id');
            $table->foreignId('order_id')->nullable(false)->change();
        });

        // ------------------------------------------



        // order_cart_accommodation_attributions

        Schema::table('order_cart_grant_deposit', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->nullable()->constrained('orders')->cascadeOnDelete();
        });

        DB::statement('
            UPDATE order_cart_grant_deposit AS oca
            JOIN order_cart AS oc ON oca.cart_id = oc.id
            SET oca.order_id = oc.order_id
        ');

        Schema::table('order_cart_grant_deposit', function (Blueprint $table) {
            $table->dropForeign('order_cart_grant_deposit_cart_id_foreign');
            $table->dropColumn('cart_id');
            $table->foreignId('order_id')->nullable(false)->change();
        });

        // ------------------------------------------




        // order_cart_sellable_deposit

        Schema::table('order_cart_sellable_deposit', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->nullable()->constrained('orders')->cascadeOnDelete();
        });

        DB::statement('
            UPDATE order_cart_sellable_deposit AS oca
            JOIN order_cart AS oc ON oca.cart_id = oc.id
            SET oca.order_id = oc.order_id
        ');

        Schema::table('order_cart_sellable_deposit', function (Blueprint $table) {
            $table->dropForeign('order_cart_sellable_deposit_cart_id_foreign');
            $table->dropColumn('cart_id');
            $table->foreignId('order_id')->nullable(false)->change();
        });

        // ------------------------------------------




        // order_cart_service

        Schema::table('order_cart_service', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->nullable()->constrained('orders')->cascadeOnDelete();
        });

        DB::statement('
            UPDATE order_cart_service AS oca
            JOIN order_cart AS oc ON oca.cart_id = oc.id
            SET oca.order_id = oc.order_id
        ');

        Schema::table('order_cart_service', function (Blueprint $table) {


            if ($this->hasForeignKey($table, 'service_cart_cart_id_foreign')) {
                $table->dropForeign('service_cart_cart_id_foreign');
            }
            if ($this->hasForeignKey($table, 'order_cart_service_cart_id_foreign')) {
                $table->dropForeign('order_cart_service_cart_id_foreign');
            }

            $table->dropColumn('cart_id');
            $table->foreignId('order_id')->nullable(false)->change();
        });

        // ------------------------------------------



        // order_cart_sellable_deposit

        Schema::table('order_cart_service_attributions', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->nullable()->constrained('orders')->cascadeOnDelete();
        });

        DB::statement('
            UPDATE order_cart_service_attributions AS oca
            JOIN order_cart AS oc ON oca.cart_id = oc.id
            SET oca.order_id = oc.order_id
        ');

        Schema::table('order_cart_service_attributions', function (Blueprint $table) {
            $table->dropForeign('order_cart_service_attributions_cart_id_foreign');
            $table->dropColumn('cart_id');
            $table->foreignId('order_id')->nullable(false)->change();
        });

        // ------------------------------------------




        // order_cart_taxroom

        Schema::table('order_cart_taxroom', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->nullable()->constrained('orders')->cascadeOnDelete();
        });

        DB::statement('
            UPDATE order_cart_taxroom AS oca
            JOIN order_cart AS oc ON oca.cart_id = oc.id
            SET oca.order_id = oc.order_id
        ');

        Schema::table('order_cart_taxroom', function (Blueprint $table) {
            $table->dropForeign('order_cart_taxroom_cart_id_foreign');
            $table->dropColumn('cart_id');
            $table->foreignId('order_id')->nullable(false)->change();
        });


        Schema::dropIfExists('order_cart');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore SQL Backup
    }
};
