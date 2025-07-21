<?php

use App\Enum\EventDepositStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_deposits', function (Blueprint $table) {
            if (false === Schema::hasColumn('event_deposits', 'paybox_num_trans')) {
                $table->string('paybox_num_trans')->nullable();
            }
            if (false === Schema::hasColumn('event_deposits', 'paybox_num_appel')) {
                $table->string('paybox_num_appel')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_deposits', function (Blueprint $table) {
            if (Schema::hasColumn('event_deposits', 'paybox_num_trans')) {
                $table->dropColumn('paybox_num_trans');
            }
            if (Schema::hasColumn('event_deposits', 'paybox_num_appel')) {
                $table->dropColumn('paybox_num_appel');
            }
        });
    }
};
