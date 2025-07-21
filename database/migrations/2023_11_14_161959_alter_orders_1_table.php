<?php

use App\Enum\OrderStatus;
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
            $table->renameColumn('payer_id','client_id');
            $table->renameColumn('price_after_tax', 'total_net');
            $table->renameColumn('price_before_tax', 'total_vat');
            $table->dropTimestamps();
            $table->dropColumn('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('client_id','payer_id');
            $table->renameColumn('total_net', 'price_after_tax');
            $table->renameColumn('total_vat', 'price_before_tax');
        });
    }
};
