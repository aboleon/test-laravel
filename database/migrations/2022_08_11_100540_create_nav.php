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
        Schema::create('nav', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('meta');
            $table->string('zone')->default('main');
            $table->unsignedBigInteger('meta_id')->nullable();
            $table->longText('title')->nullable();
            $table->longText('url')->nullable();
            $table->unsignedInteger('position')->default(1)->index();
            $table->unsignedBigInteger('parent')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nav');
    }
};
