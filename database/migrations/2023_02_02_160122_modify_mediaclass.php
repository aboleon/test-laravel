<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mediaclass', function (Blueprint $table) {
            $table->string('temp')->nullable()->index();
            $table->unsignedBigInteger('model_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mediaclass', function (Blueprint $table) {
            $table->dropColumn('temp');
            $table->unsignedBigInteger('model_id')->nullable(false)->change();
        });
    }
};
