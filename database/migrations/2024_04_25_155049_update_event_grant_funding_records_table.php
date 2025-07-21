<?php

use App\Enum\GrantFundingRecordCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('event_grant_funding_records');


        Schema::create('event_grant_funding_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grant_id')->constrained("event_grant")->onDelete('cascade');
            $table->foreignId('event_contact_id')->nullable()->constrained("events_contacts")->nullOnDelete();

            // Morph relationship columns
            $table->string('shoppable_id')->nullable();
            $table->string('shoppable_type')->nullable();

            $table->enum('shoppable_category', GrantFundingRecordCategory::keys())->default(GrantFundingRecordCategory::default())->nullable();
            $table->unsignedBigInteger('amount_net')->default(0);
            $table->unsignedBigInteger('amount_ttc')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_grant_funding_records');
    }
};
