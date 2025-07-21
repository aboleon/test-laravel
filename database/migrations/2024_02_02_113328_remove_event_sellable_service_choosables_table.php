<?php

use App\Enum\ApprovalResponseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('event_sellable_service_choosables');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
