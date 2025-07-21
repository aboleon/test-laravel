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
        Schema::table('order_cart_taxroom', function (Blueprint $table) {
            if (!Schema::hasColumn('order_cart_taxroom', 'event_contact_id')) {
                $table->foreignId('event_contact_id')->nullable()->constrained('events_contacts');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_taxroom', function (Blueprint $table) {
            if (Schema::hasColumn('order_cart_taxroom', 'event_contact_id')) {
                $table->dropForeign('order_cart_taxroom_beneficiary_event_contact_id_foreign');
                $table->dropColumn('event_contact_id');
            }
        });
    }
};
