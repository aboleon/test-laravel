<?php

use App\Enum\ClientType;
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
        Schema::create('account_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('account_type', ClientType::keys())->default(ClientType::default())->index();
            $table->foreignId('base_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('domain_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('title_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('profession_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('savant_society_id')->nullable()->references('id')->on('dictionnary_entries')->restrictOnDelete();
            $table->foreignId('billing_address')->nullable()->references('id')->on('account_address')->restrictOnDelete();
            $table->enum('civ', ['M', 'F'])->default('M')->index();
            $table->date('birth')->nullable()->index();
            $table->unsignedMediumInteger('cotisation_year')->nullable()->index();
            $table->timestamp('blacklisted')->nullable()->index();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('blacklist_comment')->nullable();
            $table->text('notes')->nullable();
            $table->string('function')->nullable();
            $table->string('passport_first_name')->nullable();
            $table->string('passport_last_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_profile');
    }
};
