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
        Schema::dropIfExists('order_cart_grant_waiver_fees');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
