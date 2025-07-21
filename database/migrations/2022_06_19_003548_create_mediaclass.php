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
        Schema::create('mediaclass', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('group')->default('media');
            $table->json('description')->nullable(true);
            $table->enum('position', ['left','right','up','down'])->default('up')->index();
            $table->string('original_filename');
            $table->string('filename',6);
            $table->string('mime');
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
        Schema::dropIfExists('mediaclass');
    }
};
