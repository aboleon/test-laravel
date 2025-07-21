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
        Schema::table('front_transactions', function (Blueprint $table) {
            $table->dropColumn('preorder_uuid');
            $table->dropColumn('order_uuid');
            $table->dropColumn('is_group_order');
            $table->dropColumn('event_contact_id');
            $table->dropColumn('lines');
            $table->dropColumn('total');
            $table->dropTimestamps();
            $table->foreignId('payment_call_id')->after('id')->nullable()->constrained('front_payment_calls');
            $table->renameColumn('transaction_return_code', 'return_code');
            $table->renameColumn('transaction_details', 'details');
            $table->renameColumn('num_trans', 'transaction_id');
            $table->renameColumn('num_appel', 'transaction_call_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_transactions', function (Blueprint $table) {
            $table->uuid('preorder_uuid');
            $table->uuid('order_uuid');
            $table->longText('lines');
            $table->unsignedInteger('total');
            $table->boolean('is_group_order')->nullable();
            $table->timestamps();
            $table->unsignedInteger('event_contact_id');
            $table->dropForeign('front_transactions_payment_call_id_foreign');
            $table->dropColumn('payment_call_id');
            $table->renameColumn('return_code', 'transaction_return_code');
            $table->renameColumn('details', 'transaction_details');
            $table->renameColumn('transaction_id', 'num_trans');
            $table->renameColumn('transaction_call_id', 'num_appel');
        });
    }
};
