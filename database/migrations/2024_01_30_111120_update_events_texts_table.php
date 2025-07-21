<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private $fields = [
        'fo_login_participant',
        'fo_login_speaker',
        'fo_login_industry',
        'fo_group',
        'fo_exhibitor',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events_texts', function (Blueprint $table) {
            foreach ($this->fields as $field) {
                if (false === Schema::hasColumn('events_texts', $field)) {
                    $table->longText($field)->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_texts', function (Blueprint $table) {
            foreach ($this->fields as $field) {
                if (true === Schema::hasColumn('events_texts', $field)) {
                    $table->dropColumn($field);
                }
            }
        });
    }
};
