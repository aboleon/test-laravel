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
        DB::statement('delete from event_grant_funding_records');

        Schema::table('event_grant_funding_records', function (Blueprint $table) {
            $table->foreignId('order_id')->after('grant_id')->constrained('orders')->cascadeOnDelete();
            $table->renameColumn('amount_net', 'amount_ht');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_grant_funding_records', function (Blueprint $table) {
            $table->dropForeign('event_grant_funding_records_order_id_foreign');
            $table->dropColumn('order_id');
            $table->renameColumn('amount_ht', 'amount_net');
        });
    }
};
