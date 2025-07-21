<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('events_transport_eligible');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            '
CREATE TABLE IF NOT EXISTS `events_transport_eligible` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `type_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_transport_eligible_event_id_type_id_unique` (`event_id`,`type_id`),
  KEY `events_transport_eligible_type_id_foreign` (`type_id`),
  CONSTRAINT `events_transport_eligible_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_transport_eligible_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `dictionnary_entries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }
};
