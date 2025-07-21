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
        Schema::table('event_shoprange', function (Blueprint $table) {
            $table->renameColumn('from','port');
            $table->renameColumn('to','order_up_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_shoprange', function (Blueprint $table) {
            $table->renameColumn('port','from');
            $table->renameColumn('order_up_to','to');
        });
    }
};
