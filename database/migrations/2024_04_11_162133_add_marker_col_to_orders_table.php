<?php

use App\Enum\OrderMarker;
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
            if (false === Schema::hasColumn('orders', 'marker')) {
                $table->enum('marker', OrderMarker::keys())->default(OrderMarker::NORMAL->value)->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'marker')) {
                $table->dropColumn('marker');
            }
        });
    }
};
