<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('events_contacts', function (Blueprint $table) {
            $table->boolean('pec_fees_apply')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events_contacts', function (Blueprint $table) {
            $table->boolean('pec_fees_apply')->nullable(false)->default(true)->change();
        });
    }
};
