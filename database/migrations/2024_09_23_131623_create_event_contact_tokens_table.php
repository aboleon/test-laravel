<?php

use App\Models\EventContact;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_contact_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EventContact::class)->constrained('events_contacts')->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('validated_at')->nullable();
        });

        Schema::table('events_contacts', function (Blueprint $table) {
            $table->dropColumn('connect_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_contact_tokens');
        Schema::table('events_contacts', function (Blueprint $table) {
            $table->uuid('connect_token')->nullable()->unique();
        });
    }
};
