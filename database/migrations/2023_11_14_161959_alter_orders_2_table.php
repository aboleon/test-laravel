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
        DB::table('orders')->delete();

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('total_net')->default(0)->change();
            $table->unsignedInteger('total_vat')->default(0)->change();
            $table->unsignedInteger('total_pec')->default(0);
            $table->enum('status', OrderStatus::keys())->default(OrderStatus::default());
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_created_by_foreign');
            $table->dropColumn('created_by');
            $table->unsignedInteger('total_net')->default(null)->change();
            $table->unsignedInteger('total_vat')->default(null)->change();
            $table->dropColumn('status');
            $table->dropColumn('total_pec');
        });
    }
};
