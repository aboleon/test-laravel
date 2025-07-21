<?php

use App\Enum\AmountType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_grant', function (Blueprint $table) {

            $table->dropColumn('amount_type');
            $table->dropColumn('amount_ht');
            $table->dropColumn('amount_ht_used');
            $table->dropColumn('amount_ttc');
            $table->dropColumn('amount_ttc_used');

        });
        Schema::table('event_grant', function (Blueprint $table) {
            $table->enum('amount_type', AmountType::values())->after('title')->default('ht');
            $table->unsignedInteger('amount')->after('amount_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('event_grant', function (Blueprint $table) {
            $table->dropColumn('amount_type');
            $table->dropColumn('amount');
        });
        Schema::table('event_grant', function (Blueprint $table) {
            $table->string('amount_type');
            $table->unsignedInteger('amount_ht');
            $table->unsignedInteger('amount_ht_used');
            $table->unsignedInteger('amount_ttc');
            $table->unsignedInteger('amount_ttc_used');
        });
    }
};
