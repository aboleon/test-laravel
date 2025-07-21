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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('group_name')->nullable();
            $table->text('billing_comment')->nullable();
            //$table->foreignId('default_billing_address')->nullable()->references('id')->on('users_address');
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onUpdate('no action')->nullOnDelete();
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
        Schema::dropIfExists('groups');
    }
};
