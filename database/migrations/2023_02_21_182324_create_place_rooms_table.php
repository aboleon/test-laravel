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
        if (!Schema::hasTable('place_rooms')) {
            Schema::create('place_rooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('place_id')->constrained()->onDelete('cascade');
                $table->json('name')->nullable();
                $table->json('level')->nullable();
                $table->string('title')->nullable();
                $table->integer('capacity')->unsigned()->nullable();
                $table->text('information')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('place_rooms');
    }
};
