<?php

use App\MailTemplates\Enum\MailTemplateFormat;
use App\MailTemplates\Enum\MailTemplateMode;
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
        Schema::create('mailtemplates', function (Blueprint $table) {
            $table->id();
            $table->timestamp('deleted_at')->nullable()->index();
            $table->json('subject');
            $table->json('content');
            $table->string('identifier')->unique();
            $table->enum('orientation', MailTemplateMode::keys())->default(MailTemplateMode::default());
            $table->enum('format', MailTemplateFormat::keys())->default(MailTemplateFormat::default());
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailtemplates');
    }
};
