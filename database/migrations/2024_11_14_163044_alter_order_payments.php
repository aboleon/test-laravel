<?php

use App\Enum\OrderOrigin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use \MetaFramework\Traits\MetaSchema;
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('order_payments', function (Blueprint $table) {
            if ($this->hasForeignKey($table, 'order_payments_transaction_id_foreign')) {
                $table->dropForeign('order_payments_transaction_id_foreign');
            }
            if ($this->hasIndex($table, 'order_payments_transaction_id_foreign')) {
                $table->dropIndex('order_payments_transaction_id_foreign');
            }

            $table->enum('transaction_origin', OrderOrigin::values())->default(OrderOrigin::default());
            $table->index(['transaction_id','transaction_origin'],'transaction_type');
        });

        DB::statement("update order_payments set transaction_origin = '".OrderOrigin::FRONT->value."' where transaction_id IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
