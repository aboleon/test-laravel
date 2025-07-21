<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('groups','group_name')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('group_name');
                $table->dropColumn('company_name');
                $table->string('name')->after('id')->index();
                $table->string('company')->after('name')->index();
            });
        }

        Schema::table('groups', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
