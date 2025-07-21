<?php

use App\Enum\OrderOrigin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (false === Schema::hasColumn('groups', 'country_code')) {
                $table->string('country_code', 2)->nullable();
            }
            if (false === Schema::hasColumn('groups', 'phone')) {
                $table->string('phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'country_code')) {
                $table->dropColumn('country_code');
            }
            if (Schema::hasColumn('groups', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
