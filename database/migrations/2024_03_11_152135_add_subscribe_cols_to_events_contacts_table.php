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
        Schema::table('events_contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('events_contacts', 'subscribe_newsletter')) {
                $table->boolean('subscribe_newsletter')->nullable();
            }
            if (!Schema::hasColumn('events_contacts', 'subscribe_sms')) {
                $table->boolean('subscribe_sms')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_contacts', function (Blueprint $table) {
            if (Schema::hasColumn('events_contacts', 'subscribe_newsletter')) {
                $table->dropColumn('subscribe_newsletter');
            }
            if (Schema::hasColumn('events_contacts', 'subscribe_sms')) {
                $table->dropColumn('subscribe_sms');
            }
        });
    }
};
