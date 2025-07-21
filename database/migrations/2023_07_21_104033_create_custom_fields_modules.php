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
        Schema::create('custom_fields_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('custom_fields')->cascadeOnDelete();
            $table->string('title');
            $table->boolean('required')->nullable();
            $table->string('type')->index();
            $table->string('subtype');
            $table->string('key', 16)->index();
            $table->unsignedInteger('position')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_fields_modules');
    }
};
