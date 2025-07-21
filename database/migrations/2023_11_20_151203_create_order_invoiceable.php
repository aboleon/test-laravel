<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use \MetaFramework\Traits\MetaSchema;

    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if ($this->hasForeignKey($table, 'orders_payer_id_foreign')) {
                $table->dropForeign('orders_payer_id_foreign');
            }
        });


        if (Schema::hasTable('order_payer')) {
            Schema::table('order_payer', function (Blueprint $table) {
                if ($this->hasForeignKey($table, 'order_payer_order_id_foreign')) {
                    $table->dropForeign('order_payer_order_id_foreign');
                }
            });
        }

        Schema::drop('order_payer');
        Schema::create('order_invoiceable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('users')->restrictOnDelete();
            $table->text('first_name');
            $table->text('last_name');
            $table->string('email')->nullable();
            $table->text('adresse_line_1');
            $table->text('adresse_line_2')->nullable();
            $table->text('adresse_line_3')->nullable();
            $table->string('zip');
            $table->text('locality');
            $table->string('cedex')->nullable();
            $table->string('country_code', 2);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_invoiceable');
    }
};
