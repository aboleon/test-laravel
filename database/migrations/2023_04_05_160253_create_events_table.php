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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamp('deleted_at')->nullable()->index();
            $table->boolean('published')->nullable()->index();
            $table->date('starts')->nullable()->index();
            $table->date('ends')->nullable()->index();
            $table->date('subs_ends')->nullable()->index();
            $table->foreignId('event_main_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('event_type_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('place_id')->references('id')->on('places')->restrictOnDelete();
            $table->foreignId('bank_account_id')->references('id')->on('bank_accounts')->restrictOnDelete();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable()->index();
            $table->foreignId('admin_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreignId('admin_subs_id')->nullable()->references('id')->on('users')->restrictOnDelete();
            $table->boolean('has_transport')->nullable()->default(1)->index();
            $table->boolean('has_abstract')->nullable()->default(1)->index();
            $table->boolean('has_external_accommodation')->nullable()->index();
            $table->unsignedSmallInteger('reminder_unpaid_accommodation')->nullable()->index();
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->restrictOnDelete();
            $table->string('bank_card_code')->nullable();
            $table->date('transport_tickets_limit_date')->nullable();
            $table->boolean('has_transferts')->nullable()->default(1);
            $table->boolean('transport_speakers')->nullable()->default(1);
            $table->boolean('transport_participants')->nullable();
            $table->boolean('transport_grant_pax')->nullable()->default(1);
            $table->longText('flags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        Schema::dropIfExists('events');
    }
};
