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
        Schema::table('accommodation_cart', function (Blueprint $table) {
            $table->renameColumn('quantity_accompagnying','quantity_accompanying');
            $table->renameColumn('accompagnying','accompanying');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accommodation_cart', function (Blueprint $table) {
            $table->renameColumn('quantity_accompanying','quantity_accompagnying');
            $table->renameColumn('accompagnying','accompanying');
        });
    }
};
