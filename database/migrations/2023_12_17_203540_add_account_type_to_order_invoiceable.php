<?php

use App\Enum\OrderClientType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->enum('account_type', OrderClientType::keys())->after('id');
            $table->unsignedBigInteger('account_id')->after('account_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->dropColumn('account_type');
            $table->dropColumn('account_id');
        });
    }
};
