<?php

use App\Enum\RegistrationType;
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
        Schema::create('users_registration', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('account_id')->nullable()->constrained('users');
            $table->enum('registration_type', RegistrationType::values())->default(RegistrationType::default());
            $table->longText('options')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('terminated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('users_registration');
    }
};
