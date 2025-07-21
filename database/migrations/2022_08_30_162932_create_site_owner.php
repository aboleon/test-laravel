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
        Schema::create('site_owner', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('manager');
            $table->string('phone');
            $table->string('vat');
            $table->string('siret');
            $table->string('email');
            $table->string('zip');
            $table->string('ville');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_owner');
    }
};
