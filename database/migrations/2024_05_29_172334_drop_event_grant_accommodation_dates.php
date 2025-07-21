<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('event_grant_accommodation_dates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {


        DB::statement("
CREATE TABLE IF NOT EXISTS `event_grant_accommodation_dates` (
    `grant_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  KEY `FK_event_grant_accommodation_dates_event_grant` (`grant_id`),
  CONSTRAINT `FK_event_grant_accommodation_dates_event_grant` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    }
};
