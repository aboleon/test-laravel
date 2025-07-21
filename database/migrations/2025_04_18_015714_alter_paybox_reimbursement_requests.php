<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paybox_reimbursement_requests', function (Blueprint $table) {
            $table->string('shoppable_type')->nullable()->after('id');
            $table->unsignedBigInteger('shoppable_id')->nullable()->after('shoppable_type');
        });

        DB::table('paybox_reimbursement_requests')
            ->whereNotNull('event_deposit_id')
            ->update([
                'shoppable_id' => DB::raw('event_deposit_id'),
                'shoppable_type' => 'App\\Models\\Order\\EventDeposit'
            ]);

        Schema::table('paybox_reimbursement_requests', function (Blueprint $table) {
            $table->dropForeign('paybox_reimbursement_requests_event_deposit_id_foreign');
            $table->dropColumn('event_deposit_id');
        });
    }

    public function down(): void
    {
        Schema::table('paybox_reimbursement_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('event_deposit_id')->nullable()->after('id');

            DB::table('paybox_reimbursement_requests')
                ->where('shoppable_type', 'App\\Models\\Order\\EventDeposit')
                ->update([
                    'event_deposit_id' => DB::raw('shoppable_id')
                ]);

            $table->dropColumn('shoppable_type');
            $table->dropColumn('shoppable_id');
        });
    }
};
