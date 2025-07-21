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
        Schema::dropIfExists('front_preorders');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement("
CREATE TABLE IF NOT EXISTS `front_preorders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `event_contact_id` bigint(20) unsigned NOT NULL,
  `lines` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_ttc` bigint(20) unsigned DEFAULT NULL,
  `total_net` bigint(20) unsigned DEFAULT NULL,
  `total_pec` int(10) unsigned DEFAULT NULL,
  `is_group_order` tinyint(1) DEFAULT NULL,
  `discharged_from_payment` tinyint(1) NOT NULL DEFAULT 0,
  `is_pec_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `pec` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `front_preorders_uuid_unique` (`uuid`),
  KEY `front_preorders_event_contact_id_foreign` (`event_contact_id`),
  CONSTRAINT `front_preorders_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
