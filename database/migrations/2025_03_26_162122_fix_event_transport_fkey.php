<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       DB::statement("ALTER TABLE `event_transport` DROP FOREIGN KEY `event_transport_transport_id_foreign`");
       DB::statement("ALTER TABLE `event_transport` ADD CONSTRAINT `FK_event_transport_participation_types` FOREIGN KEY (`transport_id`) REFERENCES `participation_types` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
