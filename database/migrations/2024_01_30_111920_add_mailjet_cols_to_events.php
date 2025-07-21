<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private $fields = [
        'mailjet_news_id',
        'mailjet_newsletter_id',
    ];


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            foreach ($this->fields as $field) {
                if (false === Schema::hasColumn('events', $field)) {
                    $table->unsignedInteger($field)->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            foreach ($this->fields as $field) {
                if (true === Schema::hasColumn('events', $field)) {
                    $table->dropColumn($field);
                }
            }
        });
    }
};
