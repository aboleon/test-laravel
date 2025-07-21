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
        Schema::table('events_contacts', function (Blueprint $table) {
            if(true === Schema::hasColumn('events_contacts', 'created_at')){
                $table->dropIndex(['created_at']);
                $table->dropColumn('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_contacts', function (Blueprint $table) {
            $table->timestamp('created_at')->index();
        });
    }
};
