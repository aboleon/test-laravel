<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_groups', function (Blueprint $table) {
            $table->boolean("is_exhibitor")->nullable();
            $table->string("password")->nullable();
            $table->unsignedSmallInteger("nb_free_badges")->nullable();
            $table->text("comment")->nullable();
            $table->text("event_comment")->nullable();
            $table->string("free_text_1")->nullable();
            $table->string("free_text_2")->nullable();
            $table->string("free_text_3")->nullable();
            $table->string("free_text_4")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_groups', function (Blueprint $table) {
            $table->dropColumn('is_exhibitor');
            $table->dropColumn('password');
            $table->dropColumn('nb_free_badges');
            $table->dropColumn('comment');
            $table->dropColumn('event_comment');
            $table->dropColumn('free_text_1');
            $table->dropColumn('free_text_2');
            $table->dropColumn('free_text_3');
            $table->dropColumn('free_text_4');
        });
    }

};
