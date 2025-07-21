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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('participation_type_id')->nullable()->constrained('participation_types')->restrictOnDelete();
        });

        DB::statement("UPDATE orders o
            LEFT JOIN (
                SELECT order_id, participation_type_id
                FROM order_cart_accommodation
                GROUP BY order_id
                HAVING MIN(id)  -- This gets the first record for each order_id
            ) oca ON o.id = oca.order_id
            SET o.participation_type_id = CASE
                WHEN oca.participation_type_id = 0 THEN NULL
                ELSE oca.participation_type_id
            END");

        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            $table->dropColumn('participation_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_accommodation', function (Blueprint $table) {
            $table->unsignedInteger('participation_type_id');
        });


        DB::statement("UPDATE order_cart_accommodation oca
JOIN orders o ON oca.order_id = o.id
SET oca.participation_type_id = CASE
    WHEN o.participation_type_id IS NULL THEN 0
    ELSE o.participation_type_id
END");

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_participation_type_id_foreign');
            $table->dropColumn('participation_type_id');
        });
    }
};
