<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->unsignedBigInteger('parent')->nullable()->index();
            $table->char('published', 1)->nullable()->index();
            $table->foreignId('author_id')->constrained('users')->onUpdate('no action')->onDelete('restrict');
            $table->unsignedBigInteger('position')->nullable()->index();
            $table->string('taxonomy')->nullable()->index();
            $table->unsignedTinyInteger('level')->default(1)->index();
            $table->text('title')->nullable();
            $table->text('title_meta')->nullable();
            $table->text('abstract')->nullable();
            $table->text('abstract_meta')->nullable();
            $table->text('url')->nullable();
            $table->string('access_key')->nullable();
            $table->string('template')->nullable();
            $table->longText('configs')->nullable();
            $table->longText('content')->nullable();
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
        Schema::dropIfExists('meta');
    }
};
