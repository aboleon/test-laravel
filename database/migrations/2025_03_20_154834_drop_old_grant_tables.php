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
        Schema::dropIfExists('event_grant_funding_records');
        Schema::dropIfExists('event_grant_allocations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
