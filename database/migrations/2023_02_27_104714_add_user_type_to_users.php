<?php

use App\Enum\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'disabled_at')) {

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('disabled_at');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', UserType::keys())->default(UserType::default())->index()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
