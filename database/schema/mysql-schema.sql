/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `account_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_address` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `billing` tinyint(1) DEFAULT NULL,
  `street_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lat` decimal(16,13) unsigned DEFAULT NULL,
  `lon` decimal(16,13) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complementary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cedex` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_address_user_id_foreign` (`user_id`),
  KEY `account_address_billing_index` (`billing`),
  CONSTRAINT `account_address_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_cards_user_id_foreign` (`user_id`),
  KEY `account_cards_expires_at_index` (`expires_at`),
  CONSTRAINT `account_cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `emitted_at` date NOT NULL,
  `expires_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_documents_user_id_foreign` (`user_id`),
  KEY `account_documents_expires_at_index` (`expires_at`),
  CONSTRAINT `account_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_full_search_view`;
/*!50001 DROP VIEW IF EXISTS `account_full_search_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `account_full_search_view` AS SELECT 
 1 AS `id`,
 1 AS `user_id`,
 1 AS `account_type`,
 1 AS `base_id`,
 1 AS `domain_id`,
 1 AS `title_id`,
 1 AS `profession_id`,
 1 AS `language_id`,
 1 AS `savant_society_id`,
 1 AS `civ`,
 1 AS `birth`,
 1 AS `cotisation_year`,
 1 AS `blacklisted`,
 1 AS `created_by`,
 1 AS `blacklist_comment`,
 1 AS `notes`,
 1 AS `function`,
 1 AS `passport_first_name`,
 1 AS `passport_last_name`,
 1 AS `rpps`,
 1 AS `establishment_id`,
 1 AS `company_name`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `is_archived`,
 1 AS `base`,
 1 AS `domain`,
 1 AS `title`,
 1 AS `profession`,
 1 AS `savant_society`,
 1 AS `created_by_fullname`,
 1 AS `establishment_name`,
 1 AS `establishment_country_code`,
 1 AS `establishment_type`,
 1 AS `establishment_street_number`,
 1 AS `establishment_postal_code`,
 1 AS `establishment_locality`,
 1 AS `establishment_administrative_area_level_1`,
 1 AS `establishment_administrative_area_level_2`,
 1 AS `establishment_text_address`,
 1 AS `address_1_street_number`,
 1 AS `address_1_route`,
 1 AS `address_1_locality`,
 1 AS `address_1_postal_code`,
 1 AS `address_1_country_code`,
 1 AS `address_1_text_address`,
 1 AS `address_1_company`,
 1 AS `address_2_street_number`,
 1 AS `address_2_route`,
 1 AS `address_2_locality`,
 1 AS `address_2_postal_code`,
 1 AS `address_2_country_code`,
 1 AS `address_2_text_address`,
 1 AS `address_2_company`,
 1 AS `address_3_street_number`,
 1 AS `address_3_route`,
 1 AS `address_3_locality`,
 1 AS `address_3_postal_code`,
 1 AS `address_3_country_code`,
 1 AS `address_3_text_address`,
 1 AS `address_3_company`,
 1 AS `phone_1`,
 1 AS `phone_2`,
 1 AS `phone_3`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `account_mails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_mails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_mails_email_unique` (`email`),
  KEY `account_mails_user_id_foreign` (`user_id`),
  CONSTRAINT `account_mails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_phones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `country_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FR',
  `default` tinyint(1) DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_phones_user_id_foreign` (`user_id`),
  KEY `account_phones_default_index` (`default`),
  KEY `account_phones_country_code_index` (`country_code`),
  CONSTRAINT `account_phones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_profile` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `account_type` enum('company','medical','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `base_id` bigint unsigned DEFAULT NULL,
  `domain_id` bigint unsigned NOT NULL,
  `profession_id` bigint unsigned NOT NULL,
  `title_id` bigint unsigned DEFAULT NULL,
  `language_id` bigint unsigned DEFAULT NULL,
  `savant_society_id` bigint unsigned DEFAULT NULL,
  `civ` enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'M',
  `birth` date DEFAULT NULL,
  `cotisation_year` mediumint unsigned DEFAULT NULL,
  `blacklisted` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `blacklist_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `function` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rpps` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `establishment_id` bigint unsigned DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_profile_user_id_foreign` (`user_id`),
  KEY `account_profile_base_id_foreign` (`base_id`),
  KEY `account_profile_domain_id_foreign` (`domain_id`),
  KEY `account_profile_title_id_foreign` (`title_id`),
  KEY `account_profile_savant_society_id_foreign` (`savant_society_id`),
  KEY `account_profile_created_by_foreign` (`created_by`),
  KEY `account_profile_account_type_index` (`account_type`),
  KEY `account_profile_civ_index` (`civ`),
  KEY `account_profile_birth_index` (`birth`),
  KEY `account_profile_cotisation_year_index` (`cotisation_year`),
  KEY `account_profile_blacklisted_index` (`blacklisted`),
  KEY `account_profile_establishment_id_foreign` (`establishment_id`),
  KEY `account_profile_language_id_foreign` (`language_id`),
  KEY `account_profile_profession_id_foreign` (`profession_id`),
  CONSTRAINT `account_profile_base_id_foreign` FOREIGN KEY (`base_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `account_profile_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `account_profile_domain_id_foreign` FOREIGN KEY (`domain_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `account_profile_establishment_id_foreign` FOREIGN KEY (`establishment_id`) REFERENCES `establishments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `account_profile_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `account_profile_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `account_profile_savant_society_id_foreign` FOREIGN KEY (`savant_society_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `account_profile_title_id_foreign` FOREIGN KEY (`title_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `account_profile_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `account_profile_export_view`;
/*!50001 DROP VIEW IF EXISTS `account_profile_export_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `account_profile_export_view` AS SELECT 
 1 AS `id`,
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `account_type`,
 1 AS `base`,
 1 AS `domain`,
 1 AS `title`,
 1 AS `profession`,
 1 AS `savant_society`,
 1 AS `civ`,
 1 AS `birth`,
 1 AS `cotisation_year`,
 1 AS `blacklisted`,
 1 AS `created_by`,
 1 AS `blacklist_comment`,
 1 AS `notes`,
 1 AS `function`,
 1 AS `passport_first_name`,
 1 AS `passport_last_name`,
 1 AS `rpps`,
 1 AS `establishment_name`,
 1 AS `establishment_country_code`,
 1 AS `establishment_type`,
 1 AS `establishment_street_number`,
 1 AS `establishment_postal_code`,
 1 AS `establishment_locality`,
 1 AS `establishment_administrative_area_level_1`,
 1 AS `establishment_administrative_area_level_2`,
 1 AS `establishment_text_address`,
 1 AS `main_address_billing`,
 1 AS `main_address_street_number`,
 1 AS `main_address_route`,
 1 AS `main_address_locality`,
 1 AS `main_address_postal_code`,
 1 AS `main_address_country_code`,
 1 AS `main_address_text_address`,
 1 AS `main_address_company`,
 1 AS `main_phone`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `account_view`;
/*!50001 DROP VIEW IF EXISTS `account_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `account_view` AS SELECT 
 1 AS `id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `deleted_at`,
 1 AS `phone`,
 1 AS `blacklisted`,
 1 AS `notes`,
 1 AS `account_type`,
 1 AS `domain`,
 1 AS `company`,
 1 AS `locality`,
 1 AS `country`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rib` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `holder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `domiciliation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iban` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `swift` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_account_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `booked_individual_from_blocked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booked_individual_from_blocked` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_group_id` bigint unsigned NOT NULL,
  `participation_type_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `quantity` smallint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `booked_individual_from_blocked_room_group_id_foreign` (`room_group_id`),
  KEY `booked_individual_from_blocked_participation_type_id_foreign` (`participation_type_id`),
  CONSTRAINT `booked_individual_from_blocked_participation_type_id_foreign` FOREIGN KEY (`participation_type_id`) REFERENCES `participation_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `booked_individual_from_blocked_room_group_id_foreign` FOREIGN KEY (`room_group_id`) REFERENCES `event_accommodation_room_groups` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `continents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `continents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `continents_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` json DEFAULT NULL,
  `continent_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `countries_continent_id_foreign` (`continent_id`),
  CONSTRAINT `countries_continent_id_foreign` FOREIGN KEY (`continent_id`) REFERENCES `continents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_fields_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_fields_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_fields_content` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `custom_fields_content_form_id_foreign` (`form_id`),
  KEY `custom_fields_content_key_index` (`key`),
  CONSTRAINT `custom_fields_content_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_fields_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_fields_modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `required` tinyint(1) DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtype` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_fields_modules_form_id_foreign` (`form_id`),
  KEY `custom_fields_modules_type_index` (`type`),
  KEY `custom_fields_modules_key_index` (`key`),
  CONSTRAINT `custom_fields_modules_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_fields_modules_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_fields_modules_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint unsigned NOT NULL,
  `key` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `custom_fields_modules_data_module_id_foreign` (`module_id`),
  KEY `custom_fields_modules_data_key_index` (`key`),
  CONSTRAINT `custom_fields_modules_data_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `custom_fields_modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dictionaries_view`;
/*!50001 DROP VIEW IF EXISTS `dictionaries_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `dictionaries_view` AS SELECT 
 1 AS `id`,
 1 AS `slug`,
 1 AS `name`,
 1 AS `type`,
 1 AS `entries_count`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `dictionnaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dictionnaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` json NOT NULL,
  `type` enum('meta','simple','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'simple',
  PRIMARY KEY (`id`),
  KEY `dictionnaries_slug_index` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dictionnary_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dictionnary_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dictionnary_id` bigint unsigned NOT NULL,
  `parent` int unsigned DEFAULT NULL,
  `position` int unsigned NOT NULL DEFAULT '0',
  `name` json NOT NULL,
  `custom` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dictionnary_entries_dictionnary_id_foreign` (`dictionnary_id`),
  KEY `dictionnary_entries_parent_index` (`parent`),
  KEY `dictionnary_entries_position_index` (`position`),
  CONSTRAINT `dictionnary_entries_dictionnary_id_foreign` FOREIGN KEY (`dictionnary_id`) REFERENCES `dictionnaries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `establishment_view`;
/*!50001 DROP VIEW IF EXISTS `establishment_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `establishment_view` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `locality`,
 1 AS `region`,
 1 AS `department`,
 1 AS `country`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `establishments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `establishments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FR',
  `type` enum('private','public') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private',
  `street_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `administrative_area_level_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lat` decimal(16,13) unsigned DEFAULT NULL,
  `lon` decimal(16,13) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `prefix` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `establishments_name_index` (`name`),
  KEY `establishments_country_code_index` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `hotel_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancellation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pec` tinyint(1) DEFAULT '1',
  `published` timestamp NULL DEFAULT NULL,
  `participation_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `total_commission` int unsigned NOT NULL DEFAULT '0',
  `turnover` int unsigned NOT NULL DEFAULT '0',
  `total_cancellation` int unsigned NOT NULL DEFAULT '0',
  `processing_fee` int unsigned NOT NULL DEFAULT '0',
  `processing_fee_vat_id` bigint unsigned DEFAULT NULL,
  `comission` int unsigned NOT NULL DEFAULT '0',
  `comission_room` int unsigned NOT NULL DEFAULT '0',
  `comission_breakfast` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_accommodation_hotel_id_event_id_unique` (`hotel_id`,`event_id`),
  KEY `event_accommodation_event_id_foreign` (`event_id`),
  KEY `event_accommodation_vat_id_foreign` (`vat_id`),
  KEY `event_accommodation_processing_fee_vat_id_foreign` (`processing_fee_vat_id`),
  CONSTRAINT `event_accommodation_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_processing_fee_vat_id_foreign` FOREIGN KEY (`processing_fee_vat_id`) REFERENCES `vat` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_blocked_group_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_blocked_group_room` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_accommodation_id` bigint unsigned NOT NULL,
  `event_group_id` bigint unsigned NOT NULL,
  `group_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `room_group_id` bigint unsigned NOT NULL,
  `total` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_event_accommodation_group_id` (`event_accommodation_id`),
  KEY `event_accommodation_blocked_group_room_event_group_id_foreign` (`event_group_id`),
  KEY `event_accommodation_blocked_group_room_room_group_id_foreign` (`room_group_id`),
  KEY `event_accommodation_blocked_group_room_group_key_index` (`group_key`),
  KEY `event_accommodation_blocked_group_room_date_index` (`date`),
  CONSTRAINT `event_accommodation_blocked_group_room_event_group_id_foreign` FOREIGN KEY (`event_group_id`) REFERENCES `event_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_blocked_group_room_room_group_id_foreign` FOREIGN KEY (`room_group_id`) REFERENCES `event_accommodation_room_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_accommodation_group_id` FOREIGN KEY (`event_accommodation_id`) REFERENCES `event_accommodation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_blocked_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_blocked_room` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_accommodation_id` bigint unsigned NOT NULL,
  `group_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `room_group_id` bigint unsigned NOT NULL,
  `total` int unsigned NOT NULL DEFAULT '0',
  `grant` int unsigned NOT NULL DEFAULT '0',
  `participation_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `event_accommodation_blocked_room_event_accommodation_id_foreign` (`event_accommodation_id`),
  KEY `event_accommodation_blocked_room_room_group_id_foreign` (`room_group_id`),
  KEY `event_accommodation_blocked_room_group_id_index` (`group_id`),
  KEY `event_accommodation_blocked_room_date_index` (`date`),
  CONSTRAINT `event_accommodation_blocked_room_event_accommodation_id_foreign` FOREIGN KEY (`event_accommodation_id`) REFERENCES `event_accommodation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_blocked_room_room_group_id_foreign` FOREIGN KEY (`room_group_id`) REFERENCES `event_accommodation_room_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_contingent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_contingent` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_accommodation_id` bigint unsigned NOT NULL,
  `room_group_id` bigint unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `total` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_accommodation_contingent_event_accommodation_id_foreign` (`event_accommodation_id`),
  KEY `event_accommodation_contingent_room_group_id_foreign` (`room_group_id`),
  KEY `event_accommodation_contingent_date_index` (`date`),
  CONSTRAINT `event_accommodation_contingent_event_accommodation_id_foreign` FOREIGN KEY (`event_accommodation_id`) REFERENCES `event_accommodation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_contingent_room_group_id_foreign` FOREIGN KEY (`room_group_id`) REFERENCES `event_accommodation_room_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_contingent_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_contingent_config` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contingent_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `buy` int unsigned DEFAULT NULL,
  `sell` int unsigned DEFAULT NULL,
  `pec` tinyint(1) DEFAULT NULL,
  `pec_allocation` int unsigned DEFAULT NULL,
  `service_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_accommodation_contingent_config_contingent_id_foreign` (`contingent_id`),
  KEY `event_accommodation_contingent_config_room_id_foreign` (`room_id`),
  KEY `event_accommodation_contingent_config_service_id_foreign` (`service_id`),
  KEY `event_accommodation_contingent_config_published_index` (`published`),
  CONSTRAINT `event_accommodation_contingent_config_contingent_id_foreign` FOREIGN KEY (`contingent_id`) REFERENCES `event_accommodation_contingent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_contingent_config_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `event_accommodation_room` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_contingent_config_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `event_accommodation_service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_deposit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_accommodation_id` bigint unsigned NOT NULL,
  `amount` int unsigned NOT NULL,
  `paid_at` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_accommodation_deposit_event_accommodation_id_foreign` (`event_accommodation_id`),
  CONSTRAINT `event_accommodation_deposit_event_accommodation_id_foreign` FOREIGN KEY (`event_accommodation_id`) REFERENCES `event_accommodation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_grant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_grant` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_accommodation_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `total` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_accommodation_grant_event_accommodation_id_foreign` (`event_accommodation_id`),
  CONSTRAINT `event_accommodation_grant_event_accommodation_id_foreign` FOREIGN KEY (`event_accommodation_id`) REFERENCES `event_accommodation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_room` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_group_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `capacity` smallint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_accommodation_room_room_group_id_room_id_unique` (`room_group_id`,`room_id`),
  KEY `event_accommodation_room_room_id_foreign` (`room_id`),
  KEY `event_accommodation_room_capacity_index` (`capacity`),
  CONSTRAINT `event_accommodation_room_room_group_id_foreign` FOREIGN KEY (`room_group_id`) REFERENCES `event_accommodation_room_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_room_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_room_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_room_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_accommodation_id` bigint unsigned NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_accommodation_room_groups_event_accommodation_id_foreign` (`event_accommodation_id`),
  CONSTRAINT `event_accommodation_room_groups_event_accommodation_id_foreign` FOREIGN KEY (`event_accommodation_id`) REFERENCES `event_accommodation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_accommodation_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_accommodation_service` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_accommodation_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int unsigned NOT NULL,
  `participation_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `event_accommodation_service_event_accommodation_id_foreign` (`event_accommodation_id`),
  KEY `event_accommodation_service_vat_id_foreign` (`vat_id`),
  CONSTRAINT `event_accommodation_service_event_accommodation_id_foreign` FOREIGN KEY (`event_accommodation_id`) REFERENCES `event_accommodation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_accommodation_service_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_contact_dashboard_choosable_view`;
/*!50001 DROP VIEW IF EXISTS `event_contact_dashboard_choosable_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_contact_dashboard_choosable_view` AS SELECT 
 1 AS `id`,
 1 AS `event_contact_id`,
 1 AS `title`,
 1 AS `date`,
 1 AS `quantity`,
 1 AS `status`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_contact_dashboard_intervention_view`;
/*!50001 DROP VIEW IF EXISTS `event_contact_dashboard_intervention_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_contact_dashboard_intervention_view` AS SELECT 
 1 AS `event_contact_id`,
 1 AS `intervention_id`,
 1 AS `session_id`,
 1 AS `status`,
 1 AS `date_fr`,
 1 AS `start_time`,
 1 AS `end_time`,
 1 AS `duration_formatted`,
 1 AS `type`,
 1 AS `title`,
 1 AS `session`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_contact_dashboard_session_view`;
/*!50001 DROP VIEW IF EXISTS `event_contact_dashboard_session_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_contact_dashboard_session_view` AS SELECT 
 1 AS `event_contact_id`,
 1 AS `session_id`,
 1 AS `status`,
 1 AS `type`,
 1 AS `session`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_contact_full_search_view`;
/*!50001 DROP VIEW IF EXISTS `event_contact_full_search_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_contact_full_search_view` AS SELECT 
 1 AS `id`,
 1 AS `user_id`,
 1 AS `account_type`,
 1 AS `base_id`,
 1 AS `domain_id`,
 1 AS `profession_id`,
 1 AS `title_id`,
 1 AS `language_id`,
 1 AS `savant_society_id`,
 1 AS `civ`,
 1 AS `birth`,
 1 AS `cotisation_year`,
 1 AS `blacklisted`,
 1 AS `created_by`,
 1 AS `blacklist_comment`,
 1 AS `notes`,
 1 AS `function`,
 1 AS `passport_first_name`,
 1 AS `passport_last_name`,
 1 AS `rpps`,
 1 AS `establishment_id`,
 1 AS `company_name`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `is_archived`,
 1 AS `base`,
 1 AS `domain`,
 1 AS `title`,
 1 AS `profession`,
 1 AS `savant_society`,
 1 AS `created_by_fullname`,
 1 AS `establishment_name`,
 1 AS `establishment_country_code`,
 1 AS `establishment_type`,
 1 AS `establishment_street_number`,
 1 AS `establishment_postal_code`,
 1 AS `establishment_locality`,
 1 AS `establishment_administrative_area_level_1`,
 1 AS `establishment_administrative_area_level_2`,
 1 AS `establishment_text_address`,
 1 AS `address_1_street_number`,
 1 AS `address_1_route`,
 1 AS `address_1_locality`,
 1 AS `address_1_postal_code`,
 1 AS `address_1_country_code`,
 1 AS `address_1_text_address`,
 1 AS `address_1_company`,
 1 AS `address_2_street_number`,
 1 AS `address_2_route`,
 1 AS `address_2_locality`,
 1 AS `address_2_postal_code`,
 1 AS `address_2_country_code`,
 1 AS `address_2_text_address`,
 1 AS `address_2_company`,
 1 AS `address_3_street_number`,
 1 AS `address_3_route`,
 1 AS `address_3_locality`,
 1 AS `address_3_postal_code`,
 1 AS `address_3_country_code`,
 1 AS `address_3_text_address`,
 1 AS `address_3_company`,
 1 AS `phone_1`,
 1 AS `phone_2`,
 1 AS `phone_3`,
 1 AS `registration_type`,
 1 AS `participation_type_id`,
 1 AS `participation_type`,
 1 AS `is_attending`,
 1 AS `comment`,
 1 AS `group`,
 1 AS `group_ids`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_contact_sellable_service_choosables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_contact_sellable_service_choosables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_contact_id` bigint unsigned NOT NULL,
  `choosable_id` bigint unsigned NOT NULL,
  `status` enum('pending','validated','denied') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `invitation_quantity_accepted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_contact_sellable_service_choosables_contact_id_foreign` (`event_contact_id`),
  KEY `event_contact_sellable_service_choosables_choosable_id_foreign` (`choosable_id`),
  KEY `event_contact_sellable_service_choosables_status_index` (`status`),
  CONSTRAINT `event_contact_sellable_service_choosables_choosable_id_foreign` FOREIGN KEY (`choosable_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_contact_sellable_service_choosables_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_contact_view`;
/*!50001 DROP VIEW IF EXISTS `event_contact_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_contact_view` AS SELECT 
 1 AS `id`,
 1 AS `event_id`,
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `domain`,
 1 AS `account_type_display`,
 1 AS `company_name`,
 1 AS `locality`,
 1 AS `country`,
 1 AS `fonction`,
 1 AS `group`,
 1 AS `group_ids`,
 1 AS `created_at`,
 1 AS `registration_type`,
 1 AS `order_cancellation`,
 1 AS `last_grant_id`,
 1 AS `pec_status`,
 1 AS `pec_status_display_fr`,
 1 AS `participation_type_group`,
 1 AS `participation_type_group_display`,
 1 AS `participation_type`,
 1 AS `nb_orders`,
 1 AS `has_something`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_deposits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `event_id` bigint unsigned NOT NULL,
  `shoppable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shoppable_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned DEFAULT NULL,
  `beneficiary_event_contact_id` bigint unsigned NOT NULL,
  `shoppable_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_net` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_vat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('paid','refunded','billed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reimbursed_at` timestamp NULL DEFAULT NULL,
  `paybox_reimbursement_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `paybox_num_trans` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paybox_num_appel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_sellable_deposits_event_id_foreign` (`event_id`),
  KEY `order_sellable_deposits_status_index` (`status`),
  KEY `event_deposits_order_id_foreign` (`order_id`),
  CONSTRAINT `event_deposits_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_sellable_deposits_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_deposits_view`;
/*!50001 DROP VIEW IF EXISTS `event_deposits_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_deposits_view` AS SELECT 
 1 AS `id`,
 1 AS `order_id`,
 1 AS `uuid`,
 1 AS `event_id`,
 1 AS `shoppable_type`,
 1 AS `shoppable_label`,
 1 AS `total_net`,
 1 AS `beneficiary_event_contact_id`,
 1 AS `status`,
 1 AS `reimbursed_at`,
 1 AS `has_invoice`,
 1 AS `is_attending_expired`,
 1 AS `total_ttc`,
 1 AS `date_fr`,
 1 AS `beneficiary_name`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_domain` (
  `event_id` bigint unsigned NOT NULL,
  `domain_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`domain_id`),
  KEY `event_domain_domain_id_foreign` (`domain_id`),
  CONSTRAINT `event_domain_domain_id_foreign` FOREIGN KEY (`domain_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `event_domain_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_front_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_front_config` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `menu_font` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_font` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `speaker_pay_room` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_front_config_event_id_foreign` (`event_id`),
  CONSTRAINT `event_front_config_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_ht` int unsigned NOT NULL,
  `amount_ht_used` bigint unsigned NOT NULL DEFAULT '0',
  `amount_ttc` int unsigned NOT NULL,
  `amount_ttc_used` bigint unsigned NOT NULL DEFAULT '0',
  `amount_type` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pax_min` int unsigned DEFAULT NULL,
  `pax_avg` int unsigned DEFAULT NULL,
  `pax_max` int unsigned DEFAULT NULL,
  `pec_fee` int unsigned NOT NULL,
  `deposit_fee` int unsigned NOT NULL,
  `manage_transport_upfront` tinyint(1) DEFAULT NULL,
  `manage_transfert_upfront` tinyint(1) DEFAULT NULL,
  `refund_transport` tinyint(1) DEFAULT NULL,
  `refund_transport_amount` int unsigned DEFAULT NULL,
  `age_eligible_min` int unsigned DEFAULT NULL,
  `age_eligible_max` int unsigned DEFAULT NULL,
  `refund_transport_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `prenotification_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_grant_event_id_foreign` (`event_id`),
  CONSTRAINT `event_grant_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_accommodation_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_accommodation_dates` (
  `grant_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  KEY `FK_event_grant_accommodation_dates_event_grant` (`grant_id`),
  CONSTRAINT `FK_event_grant_accommodation_dates_event_grant` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_address` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grant_id` bigint unsigned NOT NULL,
  `street_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lat` decimal(16,13) unsigned DEFAULT NULL,
  `lon` decimal(16,13) unsigned DEFAULT NULL,
  `complementary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_grant_address_grant_id_foreign` (`grant_id`),
  CONSTRAINT `event_grant_address_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_allocations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grant_id` bigint unsigned NOT NULL,
  `event_contact_id` bigint unsigned NOT NULL,
  `eligibility_id` bigint unsigned NOT NULL,
  `eligibility_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `continent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_grant_allocations_grant_id_foreign` (`grant_id`),
  KEY `event_grant_allocations_event_contact_id_foreign` (`event_contact_id`),
  CONSTRAINT `event_grant_allocations_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_grant_allocations_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_contact` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grant_id` bigint unsigned NOT NULL,
  `first_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fonction` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `service` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_grant_contact_grant_id_foreign` (`grant_id`),
  CONSTRAINT `event_grant_contact_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_deposit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `amount` int unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_event_grant_deposit` (`event_id`),
  CONSTRAINT `fk_event_grant_deposit` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_deposit_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_deposit_location` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `deposit_id` bigint unsigned NOT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_grant_location_deposit_id` (`deposit_id`),
  CONSTRAINT `fk_grant_location_deposit_id` FOREIGN KEY (`deposit_id`) REFERENCES `event_grant_deposit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_deposit_participation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_deposit_participation` (
  `deposit_id` bigint unsigned NOT NULL,
  `participation_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`deposit_id`,`participation_id`),
  KEY `fk_essgbpid` (`participation_id`),
  CONSTRAINT `fk_essgbpid` FOREIGN KEY (`participation_id`) REFERENCES `participation_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_grant_deposit_participation` FOREIGN KEY (`deposit_id`) REFERENCES `event_grant_deposit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_domain` (
  `grant_id` bigint unsigned NOT NULL,
  `domain_id` bigint unsigned NOT NULL,
  `pax` int unsigned DEFAULT NULL,
  PRIMARY KEY (`grant_id`,`domain_id`),
  KEY `fk_event_grant_domain_id` (`domain_id`),
  CONSTRAINT `event_grant_domain_domain_id_foreign` FOREIGN KEY (`domain_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_event_grant_domain` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_grant_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_establishments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_establishments` (
  `grant_id` bigint unsigned NOT NULL,
  `establishment_id` bigint unsigned NOT NULL,
  `pax` int unsigned DEFAULT NULL,
  PRIMARY KEY (`grant_id`,`establishment_id`),
  KEY `event_grant_establishments_establishment_id_foreign` (`establishment_id`),
  CONSTRAINT `event_grant_establishments_establishment_id_foreign` FOREIGN KEY (`establishment_id`) REFERENCES `establishments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_grant_establishments_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_establishments_view`;
/*!50001 DROP VIEW IF EXISTS `event_grant_establishments_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_grant_establishments_view` AS SELECT 
 1 AS `grant_id`,
 1 AS `pax`,
 1 AS `id`,
 1 AS `name`,
 1 AS `country_code`,
 1 AS `type`,
 1 AS `street_number`,
 1 AS `route`,
 1 AS `postal_code`,
 1 AS `locality`,
 1 AS `administrative_area_level_1`,
 1 AS `administrative_area_level_2`,
 1 AS `text_address`,
 1 AS `lat`,
 1 AS `lon`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `deleted_at`,
 1 AS `prefix`,
 1 AS `country`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_grant_funding_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_funding_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grant_id` bigint unsigned NOT NULL,
  `event_contact_id` bigint unsigned NOT NULL,
  `shoppable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shoppable_id` bigint unsigned NOT NULL,
  `shoppable_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_net` bigint unsigned NOT NULL DEFAULT '0',
  `amount_ttc` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_grant_funding_records_grant_id_foreign` (`grant_id`),
  KEY `event_grant_funding_records_event_contact_id_foreign` (`event_contact_id`),
  KEY `event_grant_funding_records_shoppable_type_shoppable_id_index` (`shoppable_type`,`shoppable_id`),
  CONSTRAINT `event_grant_funding_records_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_grant_funding_records_grant_id_foreign` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_location` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grant_id` bigint unsigned NOT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pax` int unsigned DEFAULT NULL,
  `amount` int unsigned DEFAULT NULL,
  `continent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_event_grant_location` (`grant_id`),
  CONSTRAINT `fk_event_grant_location` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_participation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_participation` (
  `grant_id` bigint unsigned NOT NULL,
  `participation_id` bigint unsigned NOT NULL,
  `pax` int unsigned DEFAULT NULL,
  PRIMARY KEY (`grant_id`,`participation_id`),
  KEY `fk_event_grant_participation_id` (`participation_id`),
  CONSTRAINT `fk_event_grant_participation` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_grant_participation_id` FOREIGN KEY (`participation_id`) REFERENCES `participation_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_profession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_grant_profession` (
  `grant_id` bigint unsigned NOT NULL,
  `profession_id` bigint unsigned NOT NULL,
  `pax` int unsigned DEFAULT NULL,
  PRIMARY KEY (`grant_id`,`profession_id`),
  KEY `fk_event_grant_profession_id` (`profession_id`),
  CONSTRAINT `fk_event_grant_profession` FOREIGN KEY (`grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_grant_profession_id` FOREIGN KEY (`profession_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_grant_view`;
/*!50001 DROP VIEW IF EXISTS `event_grant_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_grant_view` AS SELECT 
 1 AS `id`,
 1 AS `event_id`,
 1 AS `title`,
 1 AS `contact`,
 1 AS `comment`,
 1 AS `amount_ht`,
 1 AS `amount_ht_used`,
 1 AS `amount_ht_remaining`,
 1 AS `amount_ttc`,
 1 AS `amount_ttc_used`,
 1 AS `amount_ttc_remaining`,
 1 AS `amount_ht_display`,
 1 AS `amount_ht_used_display`,
 1 AS `amount_ht_remaining_display`,
 1 AS `amount_ttc_display`,
 1 AS `amount_ttc_used_display`,
 1 AS `amount_ttc_remaining_display`,
 1 AS `pec_fee`,
 1 AS `pec_fee_display`,
 1 AS `pax_avg`,
 1 AS `pax_max`,
 1 AS `active`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_group_contact_view`;
/*!50001 DROP VIEW IF EXISTS `event_group_contact_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_group_contact_view` AS SELECT 
 1 AS `id`,
 1 AS `event_id`,
 1 AS `event_group_id`,
 1 AS `group_id`,
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `locality`,
 1 AS `profile_function`,
 1 AS `country`,
 1 AS `is_main_contact`,
 1 AS `is_main_contact_display`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_group_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_group_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_group_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_group_contacts_event_group_id_foreign` (`event_group_id`),
  KEY `event_group_contacts_user_id_foreign` (`user_id`),
  CONSTRAINT `event_group_contacts_event_group_id_foreign` FOREIGN KEY (`event_group_id`) REFERENCES `event_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_group_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `group_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_exhibitor` tinyint(1) DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nb_free_badges` smallint unsigned DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `event_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `free_text_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `free_text_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `free_text_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `free_text_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_contact_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_groups_group_id_event_id_unique` (`group_id`,`event_id`),
  KEY `event_groups_event_id_foreign` (`event_id`),
  KEY `event_groups_main_contact_id_foreign` (`main_contact_id`),
  CONSTRAINT `event_groups_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_groups_main_contact_id_foreign` FOREIGN KEY (`main_contact_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_groups_view`;
/*!50001 DROP VIEW IF EXISTS `event_groups_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_groups_view` AS SELECT 
 1 AS `id`,
 1 AS `group_id`,
 1 AS `group_name`,
 1 AS `group_company`,
 1 AS `event_group_created_at`,
 1 AS `event_id`,
 1 AS `comment`,
 1 AS `user_id`,
 1 AS `main_contact_name`,
 1 AS `main_contact_email`,
 1 AS `main_contact_phone`,
 1 AS `main_contact_country`,
 1 AS `participants_count`,
 1 AS `orders_count`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_orator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_orator` (
  `event_id` bigint unsigned NOT NULL,
  `orator_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`orator_id`),
  KEY `event_orator_orator_id_foreign` (`orator_id`),
  CONSTRAINT `event_orator_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_orator_orator_id_foreign` FOREIGN KEY (`orator_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_participation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_participation` (
  `event_id` bigint unsigned NOT NULL,
  `participation_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`participation_id`),
  KEY `event_participation_participation_id_foreign` (`participation_id`),
  CONSTRAINT `event_participation_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_participation_participation_id_foreign` FOREIGN KEY (`participation_id`) REFERENCES `participation_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_pec_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_pec_domain` (
  `event_id` bigint unsigned NOT NULL,
  `domain_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`domain_id`),
  KEY `event_pec_domain_domain_id_foreign` (`domain_id`),
  CONSTRAINT `event_pec_domain_domain_id_foreign` FOREIGN KEY (`domain_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `event_pec_domain_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_pec_participation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_pec_participation` (
  `event_id` bigint unsigned NOT NULL,
  `participation_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`participation_id`),
  KEY `event_pec_participation_participation_id_foreign` (`participation_id`),
  CONSTRAINT `event_pec_participation_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_pec_participation_participation_id_foreign` FOREIGN KEY (`participation_id`) REFERENCES `participation_types` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_profession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_profession` (
  `event_id` bigint unsigned NOT NULL,
  `profession_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`profession_id`),
  KEY `event_profession_profession_id_foreign` (`profession_id`),
  CONSTRAINT `event_profession_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_profession_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_program_day_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_program_day_rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `datetime_start` datetime NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_program_days_event_id_foreign` (`event_id`),
  KEY `event_program_day_rooms_room_id_foreign` (`room_id`),
  CONSTRAINT `event_program_day_rooms_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `place_rooms` (`id`),
  CONSTRAINT `event_program_days_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_program_intervention_orators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_program_intervention_orators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `events_contacts_id` bigint unsigned NOT NULL,
  `event_program_intervention_id` bigint unsigned NOT NULL,
  `allow_pdf_distribution` tinyint(1) DEFAULT NULL,
  `allow_video_distribution` tinyint(1) DEFAULT NULL,
  `status` enum('pending','validated','denied') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `eci_events_contacts_id` (`events_contacts_id`),
  KEY `eci_epi_id` (`event_program_intervention_id`),
  CONSTRAINT `eci_epi_id` FOREIGN KEY (`event_program_intervention_id`) REFERENCES `event_program_interventions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eci_events_contacts_id` FOREIGN KEY (`events_contacts_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_program_interventions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_program_interventions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_program_session_id` bigint unsigned NOT NULL,
  `position` int NOT NULL DEFAULT '0',
  `is_online` tinyint(1) DEFAULT NULL,
  `is_visible_in_front` tinyint(1) DEFAULT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `internal_comment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `specificity_id` bigint unsigned DEFAULT NULL,
  `duration` int NOT NULL DEFAULT '0',
  `preferred_start_time` time DEFAULT NULL,
  `intervention_timing_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `start` timestamp NULL DEFAULT NULL,
  `end` timestamp NULL DEFAULT NULL,
  `sponsor_id` bigint unsigned DEFAULT NULL,
  `is_catering` tinyint(1) DEFAULT NULL,
  `is_placeholder` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_program_interventions_event_program_session_id_foreign` (`event_program_session_id`),
  KEY `event_program_interventions_specificity_id_foreign` (`specificity_id`),
  KEY `event_program_interventions_sponsor_id_foreign` (`sponsor_id`),
  CONSTRAINT `event_program_interventions_event_program_session_id_foreign` FOREIGN KEY (`event_program_session_id`) REFERENCES `event_program_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_program_interventions_specificity_id_foreign` FOREIGN KEY (`specificity_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `event_program_interventions_sponsor_id_foreign` FOREIGN KEY (`sponsor_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_program_interventions_view`;
/*!50001 DROP VIEW IF EXISTS `event_program_interventions_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_program_interventions_view` AS SELECT 
 1 AS `id`,
 1 AS `is_catering`,
 1 AS `is_placeholder`,
 1 AS `event_id`,
 1 AS `event_program_session_id`,
 1 AS `container`,
 1 AS `timings`,
 1 AS `session`,
 1 AS `name`,
 1 AS `orators`,
 1 AS `specificity`,
 1 AS `duration`,
 1 AS `is_online`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_program_session_moderators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_program_session_moderators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `events_contacts_id` bigint unsigned NOT NULL,
  `event_program_session_id` bigint unsigned NOT NULL,
  `moderator_type_id` bigint unsigned DEFAULT NULL,
  `allow_video_distribution` tinyint(1) DEFAULT NULL,
  `status` enum('pending','validated','denied') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `epsm_events_contacts_id` (`events_contacts_id`),
  KEY `epsm_eps_id` (`event_program_session_id`),
  KEY `epsm_dic_id` (`moderator_type_id`),
  CONSTRAINT `epsm_dic_id` FOREIGN KEY (`moderator_type_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `epsm_eps_id` FOREIGN KEY (`event_program_session_id`) REFERENCES `event_program_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epsm_events_contacts_id` FOREIGN KEY (`events_contacts_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_program_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_program_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_program_day_room_id` bigint unsigned NOT NULL,
  `position` int NOT NULL DEFAULT '0',
  `is_online` tinyint(1) DEFAULT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `session_type_id` bigint unsigned NOT NULL,
  `duration` int DEFAULT NULL,
  `place_room_id` bigint unsigned DEFAULT NULL,
  `is_visible_in_front` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sponsor_id` bigint unsigned DEFAULT NULL,
  `is_catering` tinyint(1) DEFAULT NULL,
  `is_placeholder` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_program_sessions_session_type_id_foreign` (`session_type_id`),
  KEY `event_program_sessions_place_room_id_foreign` (`place_room_id`),
  KEY `event_program_sessions_event_program_day_room_id_foreign` (`event_program_day_room_id`),
  KEY `event_program_sessions_sponsor_id_foreign` (`sponsor_id`),
  CONSTRAINT `event_program_sessions_event_program_day_room_id_foreign` FOREIGN KEY (`event_program_day_room_id`) REFERENCES `event_program_day_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_program_sessions_place_room_id_foreign` FOREIGN KEY (`place_room_id`) REFERENCES `place_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_program_sessions_session_type_id_foreign` FOREIGN KEY (`session_type_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `event_program_sessions_sponsor_id_foreign` FOREIGN KEY (`sponsor_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_program_sessions_view`;
/*!50001 DROP VIEW IF EXISTS `event_program_sessions_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_program_sessions_view` AS SELECT 
 1 AS `id`,
 1 AS `is_catering`,
 1 AS `is_placeholder`,
 1 AS `event_id`,
 1 AS `date`,
 1 AS `name`,
 1 AS `datetime_start`,
 1 AS `place_room`,
 1 AS `moderators`,
 1 AS `timings`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_sellable_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_sellable_service` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `service_group` bigint unsigned DEFAULT NULL,
  `service_group_combined` bigint unsigned DEFAULT NULL,
  `place_id` bigint unsigned DEFAULT NULL,
  `room_id` bigint unsigned DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `service_starts` time DEFAULT NULL,
  `service_ends` time DEFAULT NULL,
  `stock` int unsigned NOT NULL DEFAULT '0',
  `stock_initial` int unsigned NOT NULL DEFAULT '0',
  `stock_unlimited` tinyint(1) DEFAULT NULL,
  `stock_showable` int unsigned DEFAULT NULL,
  `pec_eligible` tinyint(1) DEFAULT '1',
  `pec_max_pax` int unsigned NOT NULL DEFAULT '1',
  `vat_id` bigint unsigned DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_invitation` tinyint(1) DEFAULT NULL,
  `invitation_quantity_enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_sellable_service_event_id_foreign` (`event_id`),
  KEY `event_sellable_service_service_group_foreign` (`service_group`),
  KEY `event_sellable_service_place_id_foreign` (`place_id`),
  KEY `event_sellable_service_vat_id_foreign` (`vat_id`),
  KEY `event_sellable_service_published_index` (`published`),
  KEY `event_sellable_service_service_group_combined_foreign` (`service_group_combined`),
  KEY `event_sellable_service_room_id_foreign` (`room_id`),
  CONSTRAINT `event_sellable_service_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_sellable_service_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_sellable_service_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `place_rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_sellable_service_service_group_combined_foreign` FOREIGN KEY (`service_group_combined`) REFERENCES `dictionnary_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_sellable_service_service_group_foreign` FOREIGN KEY (`service_group`) REFERENCES `dictionnary_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_sellable_service_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_sellable_service_deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_sellable_service_deposits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_sellable_service_id` bigint unsigned NOT NULL,
  `amount` int unsigned NOT NULL,
  `vat_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_deposit_essid` (`event_sellable_service_id`),
  KEY `event_sellable_service_deposits_vat_id_foreign` (`vat_id`),
  CONSTRAINT `event_sellable_service_deposits_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_deposit_essid` FOREIGN KEY (`event_sellable_service_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_sellable_service_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_sellable_service_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_sellable_service_id` bigint unsigned NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_option_essid` (`event_sellable_service_id`),
  CONSTRAINT `fk_option_essid` FOREIGN KEY (`event_sellable_service_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_sellable_service_participation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_sellable_service_participation` (
  `event_sellable_service_id` bigint unsigned NOT NULL,
  `participation_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_sellable_service_id`,`participation_id`),
  KEY `fk_event_sellable_participation_id` (`participation_id`),
  CONSTRAINT `fk_event_sellable_participation_id` FOREIGN KEY (`participation_id`) REFERENCES `participation_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ptype_essid` FOREIGN KEY (`event_sellable_service_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_sellable_service_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_sellable_service_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_sellable_service_id` bigint unsigned NOT NULL,
  `ends` date DEFAULT NULL,
  `price` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_price_essid` (`event_sellable_service_id`),
  CONSTRAINT `fk_price_essid` FOREIGN KEY (`event_sellable_service_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_sellable_service_profession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_sellable_service_profession` (
  `event_sellable_service_id` bigint unsigned NOT NULL,
  `profession_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_sellable_service_id`,`profession_id`),
  KEY `fk_event_sellable_profession_id` (`profession_id`),
  CONSTRAINT `fk_event_sellable_profession_id` FOREIGN KEY (`profession_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prof_essid` FOREIGN KEY (`event_sellable_service_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_sellable_service_view`;
/*!50001 DROP VIEW IF EXISTS `event_sellable_service_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_sellable_service_view` AS SELECT 
 1 AS `id`,
 1 AS `event_id`,
 1 AS `title_fr`,
 1 AS `is_invitation`,
 1 AS `is_invitation_display`,
 1 AS `group_fr`,
 1 AS `service_date_fr`,
 1 AS `stock_initial`,
 1 AS `reserved`,
 1 AS `stock`,
 1 AS `published`,
 1 AS `pec_eligible`,
 1 AS `prices`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_service` (
  `event_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `max` smallint unsigned NOT NULL DEFAULT '1',
  `unlimited` tinyint(1) DEFAULT NULL,
  `service_date_doesnt_count` tinyint(1) DEFAULT NULL,
  `fo_family_position` smallint unsigned DEFAULT NULL,
  PRIMARY KEY (`event_id`,`service_id`),
  KEY `event_service_service_id_foreign` (`service_id`),
  CONSTRAINT `event_service_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_shopdocs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_shopdocs` (
  `event_id` bigint unsigned NOT NULL,
  `doc_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`doc_id`),
  KEY `event_shopdocs_doc_id_foreign` (`doc_id`),
  CONSTRAINT `event_shopdocs_doc_id_foreign` FOREIGN KEY (`doc_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `event_shopdocs_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_shoprange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_shoprange` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `port` int unsigned NOT NULL,
  `order_up_to` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_shoprange_event_id_foreign` (`event_id`),
  CONSTRAINT `event_shoprange_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_transport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_transport` (
  `event_id` bigint unsigned NOT NULL,
  `transport_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`transport_id`),
  KEY `event_transport_transport_id_foreign` (`transport_id`),
  CONSTRAINT `event_transport_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_transport_transport_id_foreign` FOREIGN KEY (`transport_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_transports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_transports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `events_contacts_id` bigint unsigned NOT NULL,
  `departure_online` tinyint(1) DEFAULT NULL,
  `departure_step` bigint unsigned DEFAULT NULL,
  `departure_transport_type` bigint unsigned DEFAULT NULL,
  `departure_start_date` date DEFAULT NULL,
  `departure_start_time` time DEFAULT NULL,
  `departure_start_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_end_date` date DEFAULT NULL,
  `departure_end_time` time DEFAULT NULL,
  `departure_end_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_reference_info_participant` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `departure_participant_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `return_online` tinyint(1) DEFAULT NULL,
  `return_step` bigint unsigned DEFAULT NULL,
  `return_transport_type` bigint unsigned DEFAULT NULL,
  `return_start_date` date DEFAULT NULL,
  `return_start_time` time DEFAULT NULL,
  `return_start_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_end_date` date DEFAULT NULL,
  `return_end_time` time DEFAULT NULL,
  `return_end_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_reference_info_participant` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `return_participant_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `transfer_shuttle_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `travel_preferences` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price_before_tax` int unsigned DEFAULT NULL,
  `price_after_tax` int unsigned DEFAULT NULL,
  `max_reimbursement` int unsigned DEFAULT NULL,
  `admin_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `desired_management` enum('participant','divine','unnecessary') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_requested` tinyint(1) DEFAULT NULL,
  `request_completed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_transports_events_contacts_id_foreign` (`events_contacts_id`),
  KEY `event_transports_departure_step_foreign` (`departure_step`),
  KEY `event_transports_departure_transport_type_foreign` (`departure_transport_type`),
  KEY `event_transports_return_step_foreign` (`return_step`),
  KEY `event_transports_return_transport_type_foreign` (`return_transport_type`),
  KEY `event_transports_desired_management_index` (`desired_management`),
  CONSTRAINT `event_transports_departure_step_foreign` FOREIGN KEY (`departure_step`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `event_transports_departure_transport_type_foreign` FOREIGN KEY (`departure_transport_type`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `event_transports_events_contacts_id_foreign` FOREIGN KEY (`events_contacts_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_transports_return_step_foreign` FOREIGN KEY (`return_step`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `event_transports_return_transport_type_foreign` FOREIGN KEY (`return_transport_type`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_view`;
/*!50001 DROP VIEW IF EXISTS `event_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_view` AS SELECT 
 1 AS `id`,
 1 AS `deleted_at`,
 1 AS `codecb`,
 1 AS `starts`,
 1 AS `ends`,
 1 AS `name`,
 1 AS `subname`,
 1 AS `parent`,
 1 AS `type`,
 1 AS `admin`,
 1 AS `published`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `eventmanager_hotel_view`;
/*!50001 DROP VIEW IF EXISTS `eventmanager_hotel_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `eventmanager_hotel_view` AS SELECT 
 1 AS `id`,
 1 AS `event_id`,
 1 AS `hotel_id`,
 1 AS `bookings`,
 1 AS `title`,
 1 AS `locality`,
 1 AS `name`,
 1 AS `email`,
 1 AS `phone`,
 1 AS `pec`,
 1 AS `published`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `starts` date DEFAULT NULL,
  `ends` date DEFAULT NULL,
  `subs_ends` date DEFAULT NULL,
  `event_main_id` bigint unsigned DEFAULT NULL,
  `event_type_id` bigint unsigned NOT NULL,
  `place_id` bigint unsigned NOT NULL,
  `bank_account_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_id` bigint unsigned NOT NULL,
  `admin_subs_id` bigint unsigned DEFAULT NULL,
  `has_transport` tinyint(1) DEFAULT '1',
  `has_abstract` tinyint(1) DEFAULT '1',
  `has_program` tinyint(1) DEFAULT NULL,
  `has_external_accommodation` tinyint(1) DEFAULT NULL,
  `reminder_unpaid_accommodation` smallint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `bank_card_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transport_tickets_limit_date` date DEFAULT NULL,
  `has_transferts` tinyint(1) DEFAULT '1',
  `ask_video_authorization` tinyint(1) DEFAULT '0',
  `transport_speakers` tinyint(1) DEFAULT '1',
  `transport_participants` tinyint(1) DEFAULT NULL,
  `transport_grant_pax` tinyint(1) DEFAULT '1',
  `flags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `show_orators_picture` tinyint(1) DEFAULT '1',
  `serialized_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mailjet_news_id` int unsigned DEFAULT NULL,
  `mailjet_newsletter_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_event_main_id_foreign` (`event_main_id`),
  KEY `events_event_type_id_foreign` (`event_type_id`),
  KEY `events_place_id_foreign` (`place_id`),
  KEY `events_bank_account_id_foreign` (`bank_account_id`),
  KEY `events_admin_id_foreign` (`admin_id`),
  KEY `events_admin_subs_id_foreign` (`admin_subs_id`),
  KEY `events_created_by_foreign` (`created_by`),
  KEY `events_deleted_at_index` (`deleted_at`),
  KEY `events_published_index` (`published`),
  KEY `events_starts_index` (`starts`),
  KEY `events_ends_index` (`ends`),
  KEY `events_subs_ends_index` (`subs_ends`),
  KEY `events_created_at_index` (`created_at`),
  KEY `events_updated_at_index` (`updated_at`),
  KEY `events_has_transport_index` (`has_transport`),
  KEY `events_has_abstract_index` (`has_abstract`),
  KEY `events_has_external_accommodation_index` (`has_external_accommodation`),
  KEY `events_reminder_unpaid_accommodation_index` (`reminder_unpaid_accommodation`),
  KEY `events_has_program_index` (`has_program`),
  CONSTRAINT `events_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_admin_subs_id_foreign` FOREIGN KEY (`admin_subs_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_event_main_id_foreign` FOREIGN KEY (`event_main_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_event_type_id_foreign` FOREIGN KEY (`event_type_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `event_id` bigint unsigned NOT NULL,
  `registration_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `participation_type_id` bigint unsigned DEFAULT NULL,
  `is_attending` tinyint(1) DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fo_group_manager_request_sent` tinyint(1) DEFAULT NULL,
  `subscribe_newsletter` tinyint(1) DEFAULT NULL,
  `subscribe_sms` tinyint(1) DEFAULT NULL,
  `order_cancellation` tinyint(1) DEFAULT NULL,
  `pec_enabled` tinyint(1) DEFAULT NULL,
  `is_pec_eligible` tinyint(1) DEFAULT NULL,
  `pec_fees_apply` tinyint(1) DEFAULT NULL,
  `last_grant_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_contacts_event_id_foreign` (`event_id`),
  KEY `user_event_unique` (`user_id`,`event_id`),
  KEY `events_contacts_participation_type_id_foreign` (`participation_type_id`),
  KEY `events_contacts_participation_type_group_index` (`registration_type`),
  CONSTRAINT `events_contacts_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_contacts_participation_type_id_foreign` FOREIGN KEY (`participation_type_id`) REFERENCES `participation_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `events_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events_contacts_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events_contacts_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `events_contacts_id` bigint unsigned NOT NULL,
  `order_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` timestamp NOT NULL,
  `total_price` int unsigned NOT NULL,
  `tax_amount` int unsigned NOT NULL,
  `total_without_tax` int unsigned NOT NULL,
  `amount_paid` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_contacts_orders_order_number_unique` (`order_number`),
  KEY `events_contacts_orders_events_contacts_id_foreign` (`events_contacts_id`),
  CONSTRAINT `events_contacts_orders_events_contacts_id_foreign` FOREIGN KEY (`events_contacts_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events_contacts_orders_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events_contacts_orders_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `event_sellable_service_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` int unsigned NOT NULL,
  `total_price` int unsigned NOT NULL,
  `total_price_without_tax` int unsigned NOT NULL,
  `tax_amount` int unsigned NOT NULL,
  `event_sellable_service_price_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_contacts_orders_items_order_id_foreign` (`order_id`),
  KEY `events_contacts_orders_items_event_sellable_service_id_foreign` (`event_sellable_service_id`),
  KEY `fk_service_price` (`event_sellable_service_price_id`),
  CONSTRAINT `events_contacts_orders_items_event_sellable_service_id_foreign` FOREIGN KEY (`event_sellable_service_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_contacts_orders_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `events_contacts_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_service_price` FOREIGN KEY (`event_sellable_service_price_id`) REFERENCES `event_sellable_service_prices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events_pec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events_pec` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `admin_id` bigint unsigned DEFAULT NULL,
  `grant_admin_id` bigint unsigned DEFAULT NULL,
  `processing_fees` int unsigned DEFAULT NULL,
  `waiver_fees` int unsigned DEFAULT NULL,
  `processing_fees_vat_id` bigint unsigned DEFAULT NULL,
  `waiver_fees_vat_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_pec_event_id_foreign` (`event_id`),
  KEY `events_pec_admin_id_foreign` (`admin_id`),
  KEY `events_pec_grant_admin_id_foreign` (`grant_admin_id`),
  KEY `events_pec_processing_fees_vat_id_foreign` (`processing_fees_vat_id`),
  KEY `events_pec_waiver_fees_vat_id_foreign` (`waiver_fees_vat_id`),
  CONSTRAINT `events_pec_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_pec_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_pec_grant_admin_id_foreign` FOREIGN KEY (`grant_admin_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_pec_processing_fees_vat_id_foreign` FOREIGN KEY (`processing_fees_vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_pec_waiver_fees_vat_id_foreign` FOREIGN KEY (`waiver_fees_vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events_shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events_shops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `admin_id` bigint unsigned DEFAULT NULL,
  `shopping_limit_date` timestamp NULL DEFAULT NULL,
  `vat_id` bigint unsigned DEFAULT NULL,
  `shopping_mode` enum('fixed','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `fixed_fee` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `events_shops_event_id_foreign` (`event_id`),
  KEY `events_shops_admin_id_foreign` (`admin_id`),
  KEY `events_shops_vat_id_foreign` (`vat_id`),
  CONSTRAINT `events_shops_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `events_shops_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_shops_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events_texts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subname` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `external_accommodation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancellation_shop` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `transport_admin` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `transport_user` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `transport_unnecessary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contact_title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contact_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `max_price_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fo_login_participant` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fo_login_speaker` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fo_login_industry` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fo_group` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fo_exhibitor` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `privacy_policy_title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `privacy_policy_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `events_texts_event_id_foreign` (`event_id`),
  CONSTRAINT `events_texts_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `front_cart_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `front_cart_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `front_cart_id` bigint unsigned NOT NULL,
  `shoppable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shoppable_id` bigint unsigned NOT NULL,
  `unit_ttc` int unsigned NOT NULL DEFAULT '0',
  `quantity` mediumint unsigned NOT NULL,
  `total_net` int unsigned NOT NULL DEFAULT '0',
  `total_ttc` int unsigned NOT NULL DEFAULT '0',
  `total_pec` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `vat_id` bigint unsigned DEFAULT NULL,
  `meta_info` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `front_cart_lines_front_cart_id_foreign` (`front_cart_id`),
  KEY `front_cart_lines_shoppable_type_shoppable_id_index` (`shoppable_type`,`shoppable_id`),
  KEY `front_cart_lines_vat_id_foreign` (`vat_id`),
  CONSTRAINT `front_cart_lines_front_cart_id_foreign` FOREIGN KEY (`front_cart_id`) REFERENCES `front_carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `front_cart_lines_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `front_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `front_carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_contact_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `group_manager_event_contact_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `front_carts_event_contact_id_foreign` (`event_contact_id`),
  KEY `front_carts_group_manager_event_contact_id_foreign` (`group_manager_event_contact_id`),
  CONSTRAINT `front_carts_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `front_carts_group_manager_event_contact_id_foreign` FOREIGN KEY (`group_manager_event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `front_my_orders_view`;
/*!50001 DROP VIEW IF EXISTS `front_my_orders_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `front_my_orders_view` AS SELECT 
 1 AS `type`,
 1 AS `event_id`,
 1 AS `order_id`,
 1 AS `uuid`,
 1 AS `client_id`,
 1 AS `client_type`,
 1 AS `date`,
 1 AS `total_net`,
 1 AS `total_vat`,
 1 AS `total_ttc`,
 1 AS `total_pec`,
 1 AS `order_invoice_id`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `front_preorders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `front_preorders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_contact_id` bigint unsigned NOT NULL,
  `lines` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_ttc` bigint unsigned DEFAULT NULL,
  `total_net` bigint unsigned DEFAULT NULL,
  `total_pec` int unsigned DEFAULT NULL,
  `is_group_order` tinyint(1) DEFAULT NULL,
  `grant_id` int unsigned DEFAULT NULL,
  `grant_allocations` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `front_preorders_uuid_unique` (`uuid`),
  KEY `front_preorders_event_contact_id_foreign` (`event_contact_id`),
  CONSTRAINT `front_preorders_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `front_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `front_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `preorder_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `order_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_contact_id` bigint unsigned DEFAULT NULL,
  `lines` json DEFAULT NULL,
  `total` bigint unsigned DEFAULT NULL,
  `num_trans` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_appel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_return_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_group_order` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `front_transactions_order_uuid_unique` (`order_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `group_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group_address` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint unsigned NOT NULL,
  `billing` tinyint(1) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lat` decimal(16,13) unsigned DEFAULT NULL,
  `lon` decimal(16,13) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `complementary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cedex` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_address_group_id_foreign` (`group_id`),
  KEY `group_address_billing_index` (`billing`),
  CONSTRAINT `group_address_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `group_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `group_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_contacts_group_id_foreign` (`group_id`),
  KEY `user_group_unique` (`user_id`,`group_id`),
  CONSTRAINT `group_contacts_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `group_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `group_export_view`;
/*!50001 DROP VIEW IF EXISTS `group_export_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `group_export_view` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `company`,
 1 AS `billing_comment`,
 1 AS `siret`,
 1 AS `main_address_billing`,
 1 AS `main_address_name`,
 1 AS `main_address_street_number`,
 1 AS `main_address_route`,
 1 AS `main_address_locality`,
 1 AS `main_address_postal_code`,
 1 AS `main_address_country_code`,
 1 AS `main_address_text_address`,
 1 AS `main_address_country_name`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `group_fullsearch_view`;
/*!50001 DROP VIEW IF EXISTS `group_fullsearch_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `group_fullsearch_view` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `company`,
 1 AS `billing_comment`,
 1 AS `siret`,
 1 AS `created_by`,
 1 AS `vat_id`,
 1 AS `vat_rate`,
 1 AS `main_address_billing`,
 1 AS `main_address_name`,
 1 AS `main_address_street_number`,
 1 AS `main_address_route`,
 1 AS `main_address_locality`,
 1 AS `main_address_postal_code`,
 1 AS `main_address_country_code`,
 1 AS `main_address_text_address`,
 1 AS `main_address_country_name`,
 1 AS `creator_user_id`,
 1 AS `creator_first_name`,
 1 AS `creator_last_name`,
 1 AS `creator_email`,
 1 AS `creator_account_type`,
 1 AS `creator_base_id`,
 1 AS `creator_domain_id`,
 1 AS `creator_title_id`,
 1 AS `creator_profession_id`,
 1 AS `creator_language_id`,
 1 AS `creator_savant_society_id`,
 1 AS `creator_base`,
 1 AS `creator_domain`,
 1 AS `creator_title`,
 1 AS `creator_profession`,
 1 AS `creator_savant_society`,
 1 AS `creator_language`,
 1 AS `creator_civ`,
 1 AS `creator_birth`,
 1 AS `creator_cotisation_year`,
 1 AS `creator_blacklisted`,
 1 AS `creator_blacklist_comment`,
 1 AS `creator_notes`,
 1 AS `creator_function`,
 1 AS `creator_rpps`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `group_view`;
/*!50001 DROP VIEW IF EXISTS `group_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `group_view` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `company`,
 1 AS `phone`,
 1 AS `deleted_at`,
 1 AS `country`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `siret` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `groups_created_by_foreign` (`created_by`),
  KEY `groups_name_index` (`name`),
  KEY `groups_company_index` (`company`),
  CONSTRAINT `groups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hotel_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_address` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hotel_id` bigint unsigned NOT NULL,
  `street_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_1_short` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lat` decimal(16,13) unsigned DEFAULT NULL,
  `lon` decimal(16,13) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hotel_address_hotel_id_foreign` (`hotel_id`),
  CONSTRAINT `hotel_address_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hotel_history_view`;
/*!50001 DROP VIEW IF EXISTS `hotel_history_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `hotel_history_view` AS SELECT 
 1 AS `hotel_id`,
 1 AS `hotel`,
 1 AS `event_starts`,
 1 AS `event_ends`,
 1 AS `event`,
 1 AS `locality`,
 1 AS `country`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `hotel_view`;
/*!50001 DROP VIEW IF EXISTS `hotel_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `hotel_view` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `email`,
 1 AS `phone`,
 1 AS `locality`,
 1 AS `description`,
 1 AS `country`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `hotels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `stars` enum('1','2','3','4','5') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `services` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mailtemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailtemplates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `subject` json NOT NULL,
  `content` json NOT NULL,
  `identifier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orientation` enum('portrait','landscape') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'portrait',
  `format` enum('a4','a5') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a4',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mailtemplates_identifier_unique` (`identifier`),
  KEY `mailtemplates_deleted_at_index` (`deleted_at`),
  KEY `mailtemplates_created_at_index` (`created_at`),
  KEY `mailtemplates_updated_at_index` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_account_address_view`;
/*!50001 DROP VIEW IF EXISTS `main_account_address_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `main_account_address_view` AS SELECT 
 1 AS `id`,
 1 AS `user_id`,
 1 AS `billing`,
 1 AS `street_number`,
 1 AS `route`,
 1 AS `locality`,
 1 AS `postal_code`,
 1 AS `country_code`,
 1 AS `text_address`,
 1 AS `lat`,
 1 AS `lon`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `name`,
 1 AS `company`,
 1 AS `complementary`,
 1 AS `cedex`,
 1 AS `priority`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `main_account_phone_view`;
/*!50001 DROP VIEW IF EXISTS `main_account_phone_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `main_account_phone_view` AS SELECT 
 1 AS `id`,
 1 AS `user_id`,
 1 AS `country_code`,
 1 AS `default`,
 1 AS `phone`,
 1 AS `name`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `priority`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `main_group_address_view`;
/*!50001 DROP VIEW IF EXISTS `main_group_address_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `main_group_address_view` AS SELECT 
 1 AS `id`,
 1 AS `group_id`,
 1 AS `billing`,
 1 AS `name`,
 1 AS `street_number`,
 1 AS `route`,
 1 AS `locality`,
 1 AS `postal_code`,
 1 AS `country_code`,
 1 AS `text_address`,
 1 AS `lat`,
 1 AS `lon`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `complementary`,
 1 AS `cedex`,
 1 AS `priority`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mediaclass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mediaclass` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'media',
  `subgroup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` json DEFAULT NULL,
  `position` enum('left','right','up','down') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'up',
  `original_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `temp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mediaclass_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `mediaclass_position_index` (`position`),
  KEY `mediaclass_temp_index` (`temp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meta` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` bigint unsigned DEFAULT NULL,
  `published` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` bigint unsigned NOT NULL,
  `position` bigint unsigned DEFAULT NULL,
  `taxonomy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` tinyint unsigned NOT NULL DEFAULT '1',
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `title_meta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `abstract` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `abstract_meta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `access_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `configs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meta_author_id_foreign` (`author_id`),
  KEY `meta_type_index` (`type`),
  KEY `meta_parent_index` (`parent`),
  KEY `meta_published_index` (`published`),
  KEY `meta_position_index` (`position`),
  KEY `meta_taxonomy_index` (`taxonomy`),
  KEY `meta_level_index` (`level`),
  CONSTRAINT `meta_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nav`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nav` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'meta',
  `zone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'main',
  `meta_id` bigint unsigned DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `position` int unsigned NOT NULL DEFAULT '1',
  `parent` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nav_position_index` (`position`),
  KEY `nav_parent_index` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `newsletter_list_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_list_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `newsletter_list_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_list_contacts_newsletter_list_id_user_id_unique` (`newsletter_list_id`,`user_id`),
  KEY `newsletter_list_contacts_user_id_foreign` (`user_id`),
  CONSTRAINT `newsletter_list_contacts_newsletter_list_id_foreign` FOREIGN KEY (`newsletter_list_id`) REFERENCES `newsletter_lists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `newsletter_list_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `newsletter_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_lists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_accompanying`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_accompanying` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `total` int unsigned NOT NULL DEFAULT '0',
  `names` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `order_accompanying_order_id_foreign` (`order_id`),
  KEY `order_accompanying_room_id_foreign` (`room_id`),
  CONSTRAINT `order_accompanying_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_accompanying_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `event_accommodation_room` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `shoppable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shoppable_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `price_unit` int unsigned NOT NULL DEFAULT '0',
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `total_net` int unsigned NOT NULL DEFAULT '0',
  `total_vat` int unsigned NOT NULL DEFAULT '0',
  `total_pec` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_cart_order_id_foreign` (`order_id`),
  CONSTRAINT `order_cart_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_cart_accommodation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart_accommodation` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `event_hotel_id` bigint unsigned NOT NULL,
  `room_group_id` bigint unsigned DEFAULT NULL,
  `room_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned NOT NULL,
  `unit_price` int unsigned NOT NULL DEFAULT '0',
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `total_net` int unsigned NOT NULL DEFAULT '0',
  `total_vat` int unsigned NOT NULL DEFAULT '0',
  `total_pec` int unsigned NOT NULL DEFAULT '0',
  `participation_type_id` int unsigned NOT NULL DEFAULT '0',
  `accompanying_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `beneficiary_event_contact_id` bigint unsigned DEFAULT NULL,
  `cancellation_request` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accommodation_cart_cart_id_foreign` (`cart_id`),
  KEY `accommodation_cart_event_hotel_id_foreign` (`event_hotel_id`),
  KEY `accommodation_cart_room_id_foreign` (`room_id`),
  KEY `accommodation_cart_vat_id_foreign` (`vat_id`),
  KEY `accommodation_cart_date_index` (`date`),
  KEY `order_cart_accommodation_room_group_id_foreign` (`room_group_id`),
  KEY `order_cart_accommodation_beneficiary_event_contact_id_foreign` (`beneficiary_event_contact_id`),
  CONSTRAINT `accommodation_cart_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `order_cart` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accommodation_cart_event_hotel_id_foreign` FOREIGN KEY (`event_hotel_id`) REFERENCES `event_accommodation` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `accommodation_cart_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `event_accommodation_room` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `accommodation_cart_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `order_cart_accommodation_beneficiary_event_contact_id_foreign` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`),
  CONSTRAINT `order_cart_accommodation_room_group_id_foreign` FOREIGN KEY (`room_group_id`) REFERENCES `event_accommodation_room_groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_cart_accommodation_attributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart_accommodation_attributions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_contact_id` bigint unsigned NOT NULL,
  `cart_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `assigned_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_cart_accommodation_attributions_event_contact_id_foreign` (`event_contact_id`),
  KEY `order_cart_accommodation_attributions_cart_id_foreign` (`cart_id`),
  KEY `order_cart_accommodation_attributions_room_id_foreign` (`room_id`),
  KEY `order_cart_accommodation_attributions_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `order_cart_accommodation_attributions_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_cart_accommodation_attributions_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `order_cart` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `order_cart_accommodation_attributions_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_accommodation_attributions_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `event_accommodation_room` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_cart_grant_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart_grant_deposit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `event_grant_id` bigint unsigned NOT NULL,
  `event_deposit_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned DEFAULT NULL,
  `unit_price` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `total_net` int unsigned NOT NULL,
  `total_vat` int unsigned NOT NULL,
  `beneficiary_event_contact_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_cart_grant_deposit_cart_id_foreign` (`cart_id`),
  KEY `order_cart_grant_deposit_event_grant_id_foreign` (`event_grant_id`),
  KEY `order_cart_grant_deposit_event_deposit_id_foreign` (`event_deposit_id`),
  KEY `order_cart_grant_deposit_vat_id_foreign` (`vat_id`),
  KEY `ocgpf_beneficiary_contact_fk` (`beneficiary_event_contact_id`),
  CONSTRAINT `ocgpf_beneficiary_contact_fk` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_grant_deposit_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `order_cart` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_grant_deposit_event_deposit_id_foreign` FOREIGN KEY (`event_deposit_id`) REFERENCES `event_deposits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_grant_deposit_event_grant_id_foreign` FOREIGN KEY (`event_grant_id`) REFERENCES `event_grant` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_grant_deposit_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_cart_sellable_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart_sellable_deposit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `event_deposit_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned DEFAULT NULL,
  `unit_price` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `total_net` int unsigned NOT NULL,
  `total_vat` int unsigned NOT NULL,
  `beneficiary_event_contact_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_cart_sellable_deposit_cart_id_foreign` (`cart_id`),
  KEY `order_cart_sellable_deposit_event_deposit_id_foreign` (`event_deposit_id`),
  KEY `order_cart_sellable_deposit_vat_id_foreign` (`vat_id`),
  KEY `ocsd_beneficiary_contact_fk` (`beneficiary_event_contact_id`),
  CONSTRAINT `ocsd_beneficiary_contact_fk` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_sellable_deposit_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `order_cart` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_sellable_deposit_event_deposit_id_foreign` FOREIGN KEY (`event_deposit_id`) REFERENCES `event_deposits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_sellable_deposit_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_cart_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart_service` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned NOT NULL,
  `unit_price` int unsigned NOT NULL DEFAULT '0',
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `total_net` int unsigned NOT NULL DEFAULT '0',
  `total_vat` int unsigned NOT NULL DEFAULT '0',
  `total_pec` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `beneficiary_event_contact_id` bigint unsigned DEFAULT NULL,
  `cancellation_request` tinyint(1) DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_cart_cart_id_foreign` (`cart_id`),
  KEY `service_cart_vat_id_foreign` (`vat_id`),
  KEY `order_cart_service_service_id_foreign` (`service_id`),
  KEY `order_cart_service_beneficiary_event_contact_id_foreign` (`beneficiary_event_contact_id`),
  CONSTRAINT `order_cart_service_beneficiary_event_contact_id_foreign` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`),
  CONSTRAINT `order_cart_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `service_cart_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `order_cart` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_cart_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_cart_service_attributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart_service_attributions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_contact_id` bigint unsigned NOT NULL,
  `cart_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `assigned_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_cart_service_attributions_event_contact_id_foreign` (`event_contact_id`),
  KEY `order_cart_service_attributions_assigned_by_foreign` (`assigned_by`),
  KEY `order_cart_service_attributions_service_id_foreign` (`service_id`),
  KEY `order_cart_service_attributions_cart_id_foreign` (`cart_id`),
  CONSTRAINT `order_cart_service_attributions_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_cart_service_attributions_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `order_cart` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_service_attributions_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_service_attributions_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `event_sellable_service` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_cart_taxroom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_cart_taxroom` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `event_hotel_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `amount` int unsigned NOT NULL,
  `amount_net` int unsigned NOT NULL,
  `amount_vat` int unsigned NOT NULL,
  `vat_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `beneficiary_event_contact_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_cart_taxroom_cart_id_foreign` (`cart_id`),
  KEY `order_cart_taxroom_event_hotel_id_foreign` (`event_hotel_id`),
  KEY `order_cart_taxroom_room_id_foreign` (`room_id`),
  KEY `order_cart_taxroom_vat_id_foreign` (`vat_id`),
  KEY `order_cart_taxroom_beneficiary_event_contact_id_foreign` (`beneficiary_event_contact_id`),
  CONSTRAINT `order_cart_taxroom_beneficiary_event_contact_id_foreign` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`),
  CONSTRAINT `order_cart_taxroom_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `order_cart` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_cart_taxroom_event_hotel_id_foreign` FOREIGN KEY (`event_hotel_id`) REFERENCES `event_accommodation` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `order_cart_taxroom_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `event_accommodation_room` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `order_cart_taxroom_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_invoiceable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_invoiceable` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `account_type` enum('contact','group') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `address_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned NOT NULL,
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `locality` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cedex` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `department` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `street_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complementary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `text_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `order_invoiceable_order_id_foreign` (`order_id`),
  CONSTRAINT `order_invoiceable_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `proforma` tinyint(1) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_invoices_order_id_foreign` (`order_id`),
  KEY `order_invoices_created_by_foreign` (`created_by`),
  CONSTRAINT `order_invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_invoices_view`;
/*!50001 DROP VIEW IF EXISTS `order_invoices_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `order_invoices_view` AS SELECT 
 1 AS `id`,
 1 AS `invoice_number`,
 1 AS `created_at`,
 1 AS `order_id`,
 1 AS `event_id`,
 1 AS `uuid`,
 1 AS `client_name`,
 1 AS `total`,
 1 AS `total_paid`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `order_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_notes_order_id_foreign` (`order_id`),
  KEY `order_notes_created_by_foreign` (`created_by`),
  KEY `order_notes_updated_by_foreign` (`updated_by`),
  CONSTRAINT `order_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_notes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_notes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `amount` int unsigned NOT NULL,
  `payment_method` enum('cb_paybox','cb_vad','check','bank_transfer','cash') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `authorization_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issuer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_payments_date_index` (`date`),
  KEY `order_payments_payment_method_index` (`payment_method`),
  KEY `order_payments_order_id_foreign` (`order_id`),
  CONSTRAINT `order_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_payments_view`;
/*!50001 DROP VIEW IF EXISTS `order_payments_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `order_payments_view` AS SELECT 
 1 AS `id`,
 1 AS `invoice_number`,
 1 AS `invoice_id`,
 1 AS `order_id`,
 1 AS `event_id`,
 1 AS `uuid`,
 1 AS `payer`,
 1 AS `amount`,
 1 AS `date`,
 1 AS `authorization_number`,
 1 AS `payment_method`,
 1 AS `payment_method_translated`,
 1 AS `bank`,
 1 AS `issuer`,
 1 AS `check_number`,
 1 AS `card_number`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `order_refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_refunds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `refund_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_refunds_order_id_foreign` (`order_id`),
  KEY `order_refunds_created_by_foreign` (`created_by`),
  KEY `order_refunds_uuid_index` (`uuid`),
  CONSTRAINT `order_refunds_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `order_refunds_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_refunds_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_refunds_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `refund_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `amount` int unsigned NOT NULL,
  `object` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_refunds_items_refund_id_foreign` (`refund_id`),
  KEY `order_refunds_items_vat_id_foreign` (`vat_id`),
  CONSTRAINT `order_refunds_items_refund_id_foreign` FOREIGN KEY (`refund_id`) REFERENCES `order_refunds` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `order_refunds_items_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_refunds_view`;
/*!50001 DROP VIEW IF EXISTS `order_refunds_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `order_refunds_view` AS SELECT 
 1 AS `id`,
 1 AS `refund_number`,
 1 AS `uuid`,
 1 AS `created_at`,
 1 AS `created_at_raw`,
 1 AS `order_id`,
 1 AS `event_id`,
 1 AS `client_name`,
 1 AS `client_id`,
 1 AS `client_type`,
 1 AS `total`,
 1 AS `total_raw`,
 1 AS `vat_rate`,
 1 AS `vat_rate_raw`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `order_room_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_room_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_room_notes_order_id_foreign` (`order_id`),
  KEY `order_room_notes_room_id_foreign` (`room_id`),
  KEY `order_room_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `order_room_notes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_room_notes_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `event_accommodation_room` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_room_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_temp_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_temp_stock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shoppable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shoppable_id` bigint unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `quantity` mediumint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `room_id` bigint unsigned DEFAULT NULL,
  `participation_type_id` int unsigned NOT NULL DEFAULT '0',
  `account_type` enum('contact','group') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contact',
  `account_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_temp_stock_shoppable_type_shoppable_id_index` (`shoppable_type`,`shoppable_id`),
  KEY `order_temp_stock_uuid_index` (`uuid`),
  KEY `order_temp_stock_room_id_foreign` (`room_id`),
  KEY `order_temp_stock_account_type_index` (`account_type`),
  CONSTRAINT `order_temp_stock_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `event_accommodation_room` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `client_type` enum('contact','group') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_net` int unsigned NOT NULL DEFAULT '0',
  `total_vat` int unsigned NOT NULL DEFAULT '0',
  `total_pec` int unsigned NOT NULL DEFAULT '0',
  `status` enum('unpaid','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `origin` enum('front','back') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'back',
  `external_invoice` tinyint(1) DEFAULT NULL,
  `po` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `paybox_num_trans` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paybox_num_appel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marker` enum('normal','ghost') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  PRIMARY KEY (`id`),
  KEY `orders_event_id_foreign` (`event_id`),
  KEY `orders_payer_id_foreign` (`client_id`),
  KEY `orders_created_by_foreign` (`created_by`),
  KEY `orders_client_type_index` (`client_type`),
  KEY `orders_uuid_index` (`uuid`),
  KEY `orders_marker_index` (`marker`),
  CONSTRAINT `orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `orders_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_view`;
/*!50001 DROP VIEW IF EXISTS `orders_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `orders_view` AS SELECT 
 1 AS `id`,
 1 AS `event_id`,
 1 AS `date`,
 1 AS `client_type`,
 1 AS `origin`,
 1 AS `marker`,
 1 AS `client_type_display`,
 1 AS `client_id`,
 1 AS `status`,
 1 AS `paybox_num_trans`,
 1 AS `status_display`,
 1 AS `invoice_number`,
 1 AS `name`,
 1 AS `total`,
 1 AS `payments_total`,
 1 AS `total_pec`,
 1 AS `order_cancellation`,
 1 AS `has_invoice`,
 1 AS `has_invoice_display`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `participation_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `participation_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `group` enum('congress','industry','orator') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'congress',
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `participation_types_group_index` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `paybox_reimbursement_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paybox_reimbursement_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_deposit_id` bigint unsigned DEFAULT NULL,
  `amount` bigint unsigned NOT NULL,
  `calling_params` json DEFAULT NULL,
  `received_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paybox_reimbursement_requests_event_deposit_id_foreign` (`event_deposit_id`),
  CONSTRAINT `paybox_reimbursement_requests_event_deposit_id_foreign` FOREIGN KEY (`event_deposit_id`) REFERENCES `event_deposits` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `place_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `place_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `place_id` bigint unsigned NOT NULL,
  `lat` decimal(16,13) unsigned DEFAULT NULL,
  `lon` decimal(16,13) unsigned DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_1_short` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrative_area_level_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `place_addresses_place_id_foreign` (`place_id`),
  KEY `place_addresses_lat_index` (`lat`),
  KEY `place_addresses_lon_index` (`lon`),
  KEY `place_addresses_postal_code_index` (`postal_code`),
  KEY `place_addresses_country_code_index` (`country_code`),
  CONSTRAINT `place_addresses_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `place_room_view`;
/*!50001 DROP VIEW IF EXISTS `place_room_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `place_room_view` AS SELECT 
 1 AS `id`,
 1 AS `place_id`,
 1 AS `name`,
 1 AS `level`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `place_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `place_rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `place_id` bigint unsigned NOT NULL,
  `name` json DEFAULT NULL,
  `level` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `place_rooms_place_id_foreign` (`place_id`),
  CONSTRAINT `place_rooms_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `place_rooms_setup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `place_rooms_setup` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `place_room_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `place_rooms_setup_place_room_id_foreign` (`place_room_id`),
  CONSTRAINT `place_rooms_setup_place_room_id_foreign` FOREIGN KEY (`place_room_id`) REFERENCES `place_rooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `place_view`;
/*!50001 DROP VIEW IF EXISTS `place_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `place_view` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `email`,
 1 AS `phone`,
 1 AS `locality`,
 1 AS `type`,
 1 AS `country`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `places` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `place_type_id` bigint unsigned DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` json DEFAULT NULL,
  `access` json DEFAULT NULL,
  `more_title` json DEFAULT NULL,
  `more_description` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `places_place_type_id_foreign` (`place_type_id`),
  CONSTRAINT `places_place_type_id_foreign` FOREIGN KEY (`place_type_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `saved_searches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saved_searches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `search_filter` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `saved_searches_user_id_foreign` (`user_id`),
  KEY `saved_searches_type_index` (`type`),
  CONSTRAINT `saved_searches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sellable_view`;
/*!50001 DROP VIEW IF EXISTS `sellable_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `sellable_view` AS SELECT 
 1 AS `id`,
 1 AS `deleted_at`,
 1 AS `title_fr`,
 1 AS `title_en`,
 1 AS `price`,
 1 AS `sold_per`,
 1 AS `published`,
 1 AS `category_fr`,
 1 AS `category_en`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `sellables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sellables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned NOT NULL,
  `price` int unsigned NOT NULL,
  `price_buy` int unsigned NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sold_per` enum('unit','day') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sellables_category_id_foreign` (`category_id`),
  KEY `sellables_vat_id_foreign` (`vat_id`),
  KEY `sellables_deleted_at_index` (`deleted_at`),
  KEY `sellables_published_index` (`published`),
  KEY `sellables_sku_index` (`sku`),
  KEY `sellables_price_index` (`price`),
  KEY `sellables_price_buy_index` (`price_buy`),
  KEY `sellables_created_at_index` (`created_at`),
  KEY `sellables_updated_at_index` (`updated_at`),
  CONSTRAINT `sellables_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `dictionnary_entries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sellables_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sellables_by_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sellables_by_event` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `sellable_id` bigint unsigned NOT NULL,
  `vat_id` bigint unsigned NOT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int unsigned NOT NULL,
  `price_buy` int unsigned NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sold_per` enum('unit','day') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sellables_by_event_event_id_sellable_id_unique` (`event_id`,`sellable_id`),
  KEY `sellables_by_event_sellable_id_foreign` (`sellable_id`),
  KEY `sellables_by_event_vat_id_foreign` (`vat_id`),
  KEY `sellables_by_event_sku_index` (`sku`),
  KEY `sellables_by_event_price_index` (`price`),
  KEY `sellables_by_event_price_buy_index` (`price_buy`),
  KEY `sellables_by_event_created_at_index` (`created_at`),
  KEY `sellables_by_event_updated_at_index` (`updated_at`),
  CONSTRAINT `sellables_by_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sellables_by_event_sellable_id_foreign` FOREIGN KEY (`sellable_id`) REFERENCES `sellables` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sellables_by_event_vat_id_foreign` FOREIGN KEY (`vat_id`) REFERENCES `vat` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `site_owner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_owner` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `siret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `temporary_mails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temporary_mails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `temporary_mails_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `temporary_uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temporary_uploads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_view`;
/*!50001 DROP VIEW IF EXISTS `user_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `user_view` AS SELECT 
 1 AS `id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `deleted_at`,
 1 AS `domain`,
 1 AS `participation`,
 1 AS `company_name`,
 1 AS `locality`,
 1 AS `country`,
 1 AS `fonction`,
 1 AS `group`,
 1 AS `group_ids`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('account','system') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint unsigned DEFAULT NULL,
  `profile_photo_path` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_first_name_index` (`first_name`),
  KEY `users_last_name_index` (`last_name`),
  KEY `user_softdeleted` (`deleted_at`),
  KEY `users_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_address` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `establishment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rpps` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(16,13) unsigned DEFAULT NULL,
  `lon` decimal(16,13) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_address_user_id_foreign` (`user_id`),
  CONSTRAINT `users_address_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_administrateurs_view`;
/*!50001 DROP VIEW IF EXISTS `users_administrateurs_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `users_administrateurs_view` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `email`,
 1 AS `mobile`,
 1 AS `deleted_at`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `users_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_profile` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `job` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_profile_user_id_foreign` (`user_id`),
  CONSTRAINT `users_profile_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_roles` (
  `role_id` tinyint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  KEY `users_roles_user_id_foreign` (`user_id`),
  KEY `users_roles_role_id_index` (`role_id`),
  CONSTRAINT `users_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vat` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rate` int unsigned NOT NULL,
  `default` tinyint(1) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vat_rate_unique` (`rate`),
  KEY `vat_default_index` (`default`),
  KEY `vat_softdeleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP VIEW IF EXISTS `account_full_search_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `account_full_search_view` AS select `ap`.`id` AS `id`,`ap`.`user_id` AS `user_id`,`ap`.`account_type` AS `account_type`,`ap`.`base_id` AS `base_id`,`ap`.`domain_id` AS `domain_id`,`ap`.`title_id` AS `title_id`,`ap`.`profession_id` AS `profession_id`,`ap`.`language_id` AS `language_id`,`ap`.`savant_society_id` AS `savant_society_id`,`ap`.`civ` AS `civ`,`ap`.`birth` AS `birth`,`ap`.`cotisation_year` AS `cotisation_year`,`ap`.`blacklisted` AS `blacklisted`,`ap`.`created_by` AS `created_by`,`ap`.`blacklist_comment` AS `blacklist_comment`,`ap`.`notes` AS `notes`,`ap`.`function` AS `function`,`ap`.`passport_first_name` AS `passport_first_name`,`ap`.`passport_last_name` AS `passport_last_name`,`ap`.`rpps` AS `rpps`,`ap`.`establishment_id` AS `establishment_id`,`ap`.`company_name` AS `company_name`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,(case when (`u`.`deleted_at` is null) then 0 else 1 end) AS `is_archived`,lower(json_unquote(json_extract(`e1`.`name`,'$.fr'))) AS `base`,lower(json_unquote(json_extract(`e2`.`name`,'$.fr'))) AS `domain`,lower(json_unquote(json_extract(`e3`.`name`,'$.fr'))) AS `title`,lower(json_unquote(json_extract(`e4`.`name`,'$.fr'))) AS `profession`,lower(json_unquote(json_extract(`e5`.`name`,'$.fr'))) AS `savant_society`,concat(`creator`.`first_name`,' ',`creator`.`last_name`) AS `created_by_fullname`,`es`.`name` AS `establishment_name`,`es`.`country_code` AS `establishment_country_code`,`es`.`type` AS `establishment_type`,`es`.`street_number` AS `establishment_street_number`,`es`.`postal_code` AS `establishment_postal_code`,`es`.`locality` AS `establishment_locality`,`es`.`administrative_area_level_1` AS `establishment_administrative_area_level_1`,`es`.`administrative_area_level_2` AS `establishment_administrative_area_level_2`,`es`.`text_address` AS `establishment_text_address`,`addr1`.`street_number` AS `address_1_street_number`,`addr1`.`route` AS `address_1_route`,`addr1`.`locality` AS `address_1_locality`,`addr1`.`postal_code` AS `address_1_postal_code`,`addr1`.`country_code` AS `address_1_country_code`,`addr1`.`text_address` AS `address_1_text_address`,`addr1`.`company` AS `address_1_company`,`addr2`.`street_number` AS `address_2_street_number`,`addr2`.`route` AS `address_2_route`,`addr2`.`locality` AS `address_2_locality`,`addr2`.`postal_code` AS `address_2_postal_code`,`addr2`.`country_code` AS `address_2_country_code`,`addr2`.`text_address` AS `address_2_text_address`,`addr2`.`company` AS `address_2_company`,`addr3`.`street_number` AS `address_3_street_number`,`addr3`.`route` AS `address_3_route`,`addr3`.`locality` AS `address_3_locality`,`addr3`.`postal_code` AS `address_3_postal_code`,`addr3`.`country_code` AS `address_3_country_code`,`addr3`.`text_address` AS `address_3_text_address`,`addr3`.`company` AS `address_3_company`,`ph1`.`phone` AS `phone_1`,`ph2`.`phone` AS `phone_2`,`ph3`.`phone` AS `phone_3` from ((((((((((((((`account_profile` `ap` join `users` `u` on((`ap`.`user_id` = `u`.`id`))) join `users` `creator` on((`ap`.`created_by` = `creator`.`id`))) left join (select `account_address`.`id` AS `id`,`account_address`.`user_id` AS `user_id`,`account_address`.`billing` AS `billing`,`account_address`.`street_number` AS `street_number`,`account_address`.`route` AS `route`,`account_address`.`locality` AS `locality`,`account_address`.`postal_code` AS `postal_code`,`account_address`.`country_code` AS `country_code`,`account_address`.`text_address` AS `text_address`,`account_address`.`lat` AS `lat`,`account_address`.`lon` AS `lon`,`account_address`.`created_at` AS `created_at`,`account_address`.`updated_at` AS `updated_at`,`account_address`.`name` AS `name`,`account_address`.`company` AS `company`,`account_address`.`complementary` AS `complementary`,`account_address`.`cedex` AS `cedex`,row_number() OVER (PARTITION BY `account_address`.`user_id` ORDER BY `account_address`.`id` )  AS `rn` from `account_address`) `addr1` on(((`ap`.`user_id` = `addr1`.`user_id`) and (`addr1`.`rn` = 1)))) left join (select `account_address`.`id` AS `id`,`account_address`.`user_id` AS `user_id`,`account_address`.`billing` AS `billing`,`account_address`.`street_number` AS `street_number`,`account_address`.`route` AS `route`,`account_address`.`locality` AS `locality`,`account_address`.`postal_code` AS `postal_code`,`account_address`.`country_code` AS `country_code`,`account_address`.`text_address` AS `text_address`,`account_address`.`lat` AS `lat`,`account_address`.`lon` AS `lon`,`account_address`.`created_at` AS `created_at`,`account_address`.`updated_at` AS `updated_at`,`account_address`.`name` AS `name`,`account_address`.`company` AS `company`,`account_address`.`complementary` AS `complementary`,`account_address`.`cedex` AS `cedex`,row_number() OVER (PARTITION BY `account_address`.`user_id` ORDER BY `account_address`.`id` )  AS `rn` from `account_address`) `addr2` on(((`ap`.`user_id` = `addr2`.`user_id`) and (`addr2`.`rn` = 2)))) left join (select `account_address`.`id` AS `id`,`account_address`.`user_id` AS `user_id`,`account_address`.`billing` AS `billing`,`account_address`.`street_number` AS `street_number`,`account_address`.`route` AS `route`,`account_address`.`locality` AS `locality`,`account_address`.`postal_code` AS `postal_code`,`account_address`.`country_code` AS `country_code`,`account_address`.`text_address` AS `text_address`,`account_address`.`lat` AS `lat`,`account_address`.`lon` AS `lon`,`account_address`.`created_at` AS `created_at`,`account_address`.`updated_at` AS `updated_at`,`account_address`.`name` AS `name`,`account_address`.`company` AS `company`,`account_address`.`complementary` AS `complementary`,`account_address`.`cedex` AS `cedex`,row_number() OVER (PARTITION BY `account_address`.`user_id` ORDER BY `account_address`.`id` )  AS `rn` from `account_address`) `addr3` on(((`ap`.`user_id` = `addr3`.`user_id`) and (`addr3`.`rn` = 3)))) left join (select `account_phones`.`id` AS `id`,`account_phones`.`user_id` AS `user_id`,`account_phones`.`country_code` AS `country_code`,`account_phones`.`default` AS `default`,`account_phones`.`phone` AS `phone`,`account_phones`.`name` AS `name`,`account_phones`.`created_at` AS `created_at`,`account_phones`.`updated_at` AS `updated_at`,row_number() OVER (PARTITION BY `account_phones`.`user_id` ORDER BY `account_phones`.`id` )  AS `rn` from `account_phones`) `ph1` on(((`ap`.`user_id` = `ph1`.`user_id`) and (`ph1`.`rn` = 1)))) left join (select `account_phones`.`id` AS `id`,`account_phones`.`user_id` AS `user_id`,`account_phones`.`country_code` AS `country_code`,`account_phones`.`default` AS `default`,`account_phones`.`phone` AS `phone`,`account_phones`.`name` AS `name`,`account_phones`.`created_at` AS `created_at`,`account_phones`.`updated_at` AS `updated_at`,row_number() OVER (PARTITION BY `account_phones`.`user_id` ORDER BY `account_phones`.`id` )  AS `rn` from `account_phones`) `ph2` on(((`ap`.`user_id` = `ph2`.`user_id`) and (`ph2`.`rn` = 2)))) left join (select `account_phones`.`id` AS `id`,`account_phones`.`user_id` AS `user_id`,`account_phones`.`country_code` AS `country_code`,`account_phones`.`default` AS `default`,`account_phones`.`phone` AS `phone`,`account_phones`.`name` AS `name`,`account_phones`.`created_at` AS `created_at`,`account_phones`.`updated_at` AS `updated_at`,row_number() OVER (PARTITION BY `account_phones`.`user_id` ORDER BY `account_phones`.`id` )  AS `rn` from `account_phones`) `ph3` on(((`ap`.`user_id` = `ph3`.`user_id`) and (`ph3`.`rn` = 3)))) left join `dictionnary_entries` `e1` on((`ap`.`base_id` = `e1`.`id`))) left join `dictionnary_entries` `e2` on((`ap`.`domain_id` = `e2`.`id`))) left join `dictionnary_entries` `e3` on((`ap`.`title_id` = `e3`.`id`))) left join `dictionnary_entries` `e4` on((`ap`.`profession_id` = `e4`.`id`))) left join `dictionnary_entries` `e5` on((`ap`.`savant_society_id` = `e5`.`id`))) left join `establishments` `es` on((`ap`.`establishment_id` = `es`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `account_profile_export_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `account_profile_export_view` AS select `ap`.`id` AS `id`,`ap`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`ap`.`account_type` AS `account_type`,json_unquote(json_extract(`e1`.`name`,'$.fr')) AS `base`,json_unquote(json_extract(`e2`.`name`,'$.fr')) AS `domain`,json_unquote(json_extract(`e3`.`name`,'$.fr')) AS `title`,json_unquote(json_extract(`e4`.`name`,'$.fr')) AS `profession`,json_unquote(json_extract(`e5`.`name`,'$.fr')) AS `savant_society`,`ap`.`civ` AS `civ`,`ap`.`birth` AS `birth`,`ap`.`cotisation_year` AS `cotisation_year`,`ap`.`blacklisted` AS `blacklisted`,concat(`creator`.`first_name`,' ',`creator`.`last_name`) AS `created_by`,`ap`.`blacklist_comment` AS `blacklist_comment`,`ap`.`notes` AS `notes`,`ap`.`function` AS `function`,`ap`.`passport_first_name` AS `passport_first_name`,`ap`.`passport_last_name` AS `passport_last_name`,`ap`.`rpps` AS `rpps`,`es`.`name` AS `establishment_name`,`es`.`country_code` AS `establishment_country_code`,`es`.`type` AS `establishment_type`,`es`.`street_number` AS `establishment_street_number`,`es`.`postal_code` AS `establishment_postal_code`,`es`.`locality` AS `establishment_locality`,`es`.`administrative_area_level_1` AS `establishment_administrative_area_level_1`,`es`.`administrative_area_level_2` AS `establishment_administrative_area_level_2`,`es`.`text_address` AS `establishment_text_address`,(case when (`addr`.`billing` = 1) then 'Oui' else 'Non' end) AS `main_address_billing`,`addr`.`street_number` AS `main_address_street_number`,`addr`.`route` AS `main_address_route`,`addr`.`locality` AS `main_address_locality`,`addr`.`postal_code` AS `main_address_postal_code`,`addr`.`country_code` AS `main_address_country_code`,`addr`.`text_address` AS `main_address_text_address`,`addr`.`company` AS `main_address_company`,`ph`.`phone` AS `main_phone` from ((((((((((`account_profile` `ap` join `users` `u` on((`ap`.`user_id` = `u`.`id`))) join `users` `creator` on((`ap`.`created_by` = `creator`.`id`))) left join `main_account_address_view` `addr` on((`ap`.`user_id` = `addr`.`user_id`))) left join `main_account_phone_view` `ph` on((`ap`.`user_id` = `ph`.`user_id`))) left join `dictionnary_entries` `e1` on((`ap`.`base_id` = `e1`.`id`))) left join `dictionnary_entries` `e2` on((`ap`.`domain_id` = `e2`.`id`))) left join `dictionnary_entries` `e3` on((`ap`.`title_id` = `e3`.`id`))) left join `dictionnary_entries` `e4` on((`ap`.`profession_id` = `e4`.`id`))) left join `dictionnary_entries` `e5` on((`ap`.`savant_society_id` = `e5`.`id`))) left join `establishments` `es` on((`ap`.`establishment_id` = `es`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `account_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `account_view` AS select `a`.`id` AS `id`,`a`.`first_name` AS `first_name`,`a`.`last_name` AS `last_name`,`a`.`email` AS `email`,`a`.`deleted_at` AS `deleted_at`,`ph`.`phone` AS `phone`,`b`.`blacklisted` AS `blacklisted`,`b`.`notes` AS `notes`,`b`.`account_type` AS `account_type`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `domain`,`addr`.`company` AS `company`,`addr`.`locality` AS `locality`,json_unquote(json_extract(`cn`.`name`,'$.fr')) AS `country` from (((((`users` `a` join `account_profile` `b` on((`a`.`id` = `b`.`user_id`))) left join `dictionnary_entries` `c` on((`c`.`id` = `b`.`domain_id`))) left join `main_account_address_view` `addr` on((`a`.`id` = `addr`.`user_id`))) left join `main_account_phone_view` `ph` on((`a`.`id` = `ph`.`user_id`))) left join `countries` `cn` on((`addr`.`country_code` = `cn`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `dictionaries_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `dictionaries_view` AS select `d`.`id` AS `id`,`d`.`slug` AS `slug`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `name`,`d`.`type` AS `type`,coalesce(`entries`.`entries_count`,0) AS `entries_count` from (`dictionnaries` `d` left join (select `dictionnary_entries`.`dictionnary_id` AS `dictionnary_id`,count(0) AS `entries_count` from `dictionnary_entries` where ((`dictionnary_entries`.`parent` is null) and (`dictionnary_entries`.`deleted_at` is null)) group by `dictionnary_entries`.`dictionnary_id`) `entries` on((`d`.`id` = `entries`.`dictionnary_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `establishment_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `establishment_view` AS select `a`.`id` AS `id`,`a`.`name` AS `name`,`a`.`locality` AS `locality`,`a`.`administrative_area_level_1` AS `region`,`a`.`administrative_area_level_2` AS `department`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country` from (`establishments` `a` left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_contact_dashboard_choosable_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_contact_dashboard_choosable_view` AS select `c`.`id` AS `id`,`ec`.`id` AS `event_contact_id`,lower(json_unquote(json_extract(`s`.`title`,'$.fr'))) AS `title`,date_format(`s`.`service_date`,'%d/%m/%Y') AS `date`,(case when ((`c`.`status` = 'validated') and (`c`.`invitation_quantity_accepted` is null)) then 1 when ((`c`.`status` = 'validated') and (`c`.`invitation_quantity_accepted` = 1)) then 2 else 0 end) AS `quantity`,(case `c`.`status` when 'pending' then 'En attente' when 'validated' then 'Validé' when 'denied' then 'Refusé' end) AS `status` from ((`event_contact_sellable_service_choosables` `c` join `event_sellable_service` `s` on((`s`.`id` = `c`.`choosable_id`))) join `events_contacts` `ec` on((`ec`.`id` = `c`.`event_contact_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_contact_dashboard_intervention_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_contact_dashboard_intervention_view` AS select `ec`.`id` AS `event_contact_id`,`epi`.`id` AS `intervention_id`,`eps`.`id` AS `session_id`,`epio`.`status` AS `status`,date_format(`epi`.`start`,'%d/%m/%Y') AS `date_fr`,date_format(`epi`.`start`,'%Hh%i') AS `start_time`,date_format(`epi`.`end`,'%Hh%i') AS `end_time`,(case when (floor((`epi`.`duration` / 60)) > 0) then concat(lpad(floor((`epi`.`duration` / 60)),2,'0'),'h',lpad((`epi`.`duration` % 60),2,'0'),'m') else concat((`epi`.`duration` % 60),'m') end) AS `duration_formatted`,if((`ide`.`id` is not null),json_unquote(json_extract(`ide`.`name`,'$.fr')),'Orateur') AS `type`,json_unquote(json_extract(`epi`.`name`,'$.fr')) AS `title`,json_unquote(json_extract(`eps`.`name`,'$.fr')) AS `session` from ((((`event_program_interventions` `epi` join `event_program_sessions` `eps` on((`epi`.`event_program_session_id` = `eps`.`id`))) left join `event_program_intervention_orators` `epio` on((`epio`.`event_program_intervention_id` = `epi`.`id`))) left join `events_contacts` `ec` on((`epio`.`events_contacts_id` = `ec`.`id`))) left join `dictionnary_entries` `ide` on((`epi`.`specificity_id` = `ide`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_contact_dashboard_session_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_contact_dashboard_session_view` AS select `ec2`.`id` AS `event_contact_id`,`eps2`.`id` AS `session_id`,`epsm`.`status` AS `status`,if((`mde`.`id` is not null),json_unquote(json_extract(`mde`.`name`,'$.fr')),'Modérateur') AS `type`,json_unquote(json_extract(`eps2`.`name`,'$.fr')) AS `session` from (((`event_program_sessions` `eps2` left join `event_program_session_moderators` `epsm` on((`epsm`.`event_program_session_id` = `eps2`.`id`))) left join `events_contacts` `ec2` on((`epsm`.`events_contacts_id` = `ec2`.`id`))) left join `dictionnary_entries` `mde` on((`epsm`.`moderator_type_id` = `mde`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_contact_full_search_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_contact_full_search_view` AS select `ap`.`id` AS `id`,`ap`.`user_id` AS `user_id`,`ap`.`account_type` AS `account_type`,`ap`.`base_id` AS `base_id`,`ap`.`domain_id` AS `domain_id`,`ap`.`profession_id` AS `profession_id`,`ap`.`title_id` AS `title_id`,`ap`.`language_id` AS `language_id`,`ap`.`savant_society_id` AS `savant_society_id`,`ap`.`civ` AS `civ`,`ap`.`birth` AS `birth`,`ap`.`cotisation_year` AS `cotisation_year`,`ap`.`blacklisted` AS `blacklisted`,`ap`.`created_by` AS `created_by`,`ap`.`blacklist_comment` AS `blacklist_comment`,`ap`.`notes` AS `notes`,`ap`.`function` AS `function`,`ap`.`passport_first_name` AS `passport_first_name`,`ap`.`passport_last_name` AS `passport_last_name`,`ap`.`rpps` AS `rpps`,`ap`.`establishment_id` AS `establishment_id`,`ap`.`company_name` AS `company_name`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,(case when (`u`.`deleted_at` is null) then 0 else 1 end) AS `is_archived`,lower(json_unquote(json_extract(`e1`.`name`,'$.fr'))) AS `base`,lower(json_unquote(json_extract(`e2`.`name`,'$.fr'))) AS `domain`,lower(json_unquote(json_extract(`e3`.`name`,'$.fr'))) AS `title`,lower(json_unquote(json_extract(`e4`.`name`,'$.fr'))) AS `profession`,lower(json_unquote(json_extract(`e5`.`name`,'$.fr'))) AS `savant_society`,concat(`creator`.`first_name`,' ',`creator`.`last_name`) AS `created_by_fullname`,`es`.`name` AS `establishment_name`,`es`.`country_code` AS `establishment_country_code`,`es`.`type` AS `establishment_type`,`es`.`street_number` AS `establishment_street_number`,`es`.`postal_code` AS `establishment_postal_code`,`es`.`locality` AS `establishment_locality`,`es`.`administrative_area_level_1` AS `establishment_administrative_area_level_1`,`es`.`administrative_area_level_2` AS `establishment_administrative_area_level_2`,`es`.`text_address` AS `establishment_text_address`,`addr1`.`street_number` AS `address_1_street_number`,`addr1`.`route` AS `address_1_route`,`addr1`.`locality` AS `address_1_locality`,`addr1`.`postal_code` AS `address_1_postal_code`,`addr1`.`country_code` AS `address_1_country_code`,`addr1`.`text_address` AS `address_1_text_address`,`addr1`.`company` AS `address_1_company`,`addr2`.`street_number` AS `address_2_street_number`,`addr2`.`route` AS `address_2_route`,`addr2`.`locality` AS `address_2_locality`,`addr2`.`postal_code` AS `address_2_postal_code`,`addr2`.`country_code` AS `address_2_country_code`,`addr2`.`text_address` AS `address_2_text_address`,`addr2`.`company` AS `address_2_company`,`addr3`.`street_number` AS `address_3_street_number`,`addr3`.`route` AS `address_3_route`,`addr3`.`locality` AS `address_3_locality`,`addr3`.`postal_code` AS `address_3_postal_code`,`addr3`.`country_code` AS `address_3_country_code`,`addr3`.`text_address` AS `address_3_text_address`,`addr3`.`company` AS `address_3_company`,`ph1`.`phone` AS `phone_1`,`ph2`.`phone` AS `phone_2`,`ph3`.`phone` AS `phone_3`,`ec`.`registration_type` AS `registration_type`,`ec`.`participation_type_id` AS `participation_type_id`,lower(json_unquote(json_extract(`pt`.`name`,'$.fr'))) AS `participation_type`,`ec`.`is_attending` AS `is_attending`,`ec`.`comment` AS `comment`,`grp`.`group_names` AS `group`,`grp`.`group_ids` AS `group_ids` from (((((((((((((((((`account_profile` `ap` join `users` `u` on((`ap`.`user_id` = `u`.`id`))) join `users` `creator` on((`ap`.`created_by` = `creator`.`id`))) left join (select `account_address`.`id` AS `id`,`account_address`.`user_id` AS `user_id`,`account_address`.`billing` AS `billing`,`account_address`.`street_number` AS `street_number`,`account_address`.`route` AS `route`,`account_address`.`locality` AS `locality`,`account_address`.`postal_code` AS `postal_code`,`account_address`.`country_code` AS `country_code`,`account_address`.`text_address` AS `text_address`,`account_address`.`lat` AS `lat`,`account_address`.`lon` AS `lon`,`account_address`.`created_at` AS `created_at`,`account_address`.`updated_at` AS `updated_at`,`account_address`.`name` AS `name`,`account_address`.`company` AS `company`,`account_address`.`complementary` AS `complementary`,`account_address`.`cedex` AS `cedex`,row_number() OVER (PARTITION BY `account_address`.`user_id` ORDER BY `account_address`.`id` )  AS `rn` from `account_address`) `addr1` on(((`ap`.`user_id` = `addr1`.`user_id`) and (`addr1`.`rn` = 1)))) left join (select `account_address`.`id` AS `id`,`account_address`.`user_id` AS `user_id`,`account_address`.`billing` AS `billing`,`account_address`.`street_number` AS `street_number`,`account_address`.`route` AS `route`,`account_address`.`locality` AS `locality`,`account_address`.`postal_code` AS `postal_code`,`account_address`.`country_code` AS `country_code`,`account_address`.`text_address` AS `text_address`,`account_address`.`lat` AS `lat`,`account_address`.`lon` AS `lon`,`account_address`.`created_at` AS `created_at`,`account_address`.`updated_at` AS `updated_at`,`account_address`.`name` AS `name`,`account_address`.`company` AS `company`,`account_address`.`complementary` AS `complementary`,`account_address`.`cedex` AS `cedex`,row_number() OVER (PARTITION BY `account_address`.`user_id` ORDER BY `account_address`.`id` )  AS `rn` from `account_address`) `addr2` on(((`ap`.`user_id` = `addr2`.`user_id`) and (`addr2`.`rn` = 2)))) left join (select `account_address`.`id` AS `id`,`account_address`.`user_id` AS `user_id`,`account_address`.`billing` AS `billing`,`account_address`.`street_number` AS `street_number`,`account_address`.`route` AS `route`,`account_address`.`locality` AS `locality`,`account_address`.`postal_code` AS `postal_code`,`account_address`.`country_code` AS `country_code`,`account_address`.`text_address` AS `text_address`,`account_address`.`lat` AS `lat`,`account_address`.`lon` AS `lon`,`account_address`.`created_at` AS `created_at`,`account_address`.`updated_at` AS `updated_at`,`account_address`.`name` AS `name`,`account_address`.`company` AS `company`,`account_address`.`complementary` AS `complementary`,`account_address`.`cedex` AS `cedex`,row_number() OVER (PARTITION BY `account_address`.`user_id` ORDER BY `account_address`.`id` )  AS `rn` from `account_address`) `addr3` on(((`ap`.`user_id` = `addr3`.`user_id`) and (`addr3`.`rn` = 3)))) left join (select `account_phones`.`id` AS `id`,`account_phones`.`user_id` AS `user_id`,`account_phones`.`country_code` AS `country_code`,`account_phones`.`default` AS `default`,`account_phones`.`phone` AS `phone`,`account_phones`.`name` AS `name`,`account_phones`.`created_at` AS `created_at`,`account_phones`.`updated_at` AS `updated_at`,row_number() OVER (PARTITION BY `account_phones`.`user_id` ORDER BY `account_phones`.`id` )  AS `rn` from `account_phones`) `ph1` on(((`ap`.`user_id` = `ph1`.`user_id`) and (`ph1`.`rn` = 1)))) left join (select `account_phones`.`id` AS `id`,`account_phones`.`user_id` AS `user_id`,`account_phones`.`country_code` AS `country_code`,`account_phones`.`default` AS `default`,`account_phones`.`phone` AS `phone`,`account_phones`.`name` AS `name`,`account_phones`.`created_at` AS `created_at`,`account_phones`.`updated_at` AS `updated_at`,row_number() OVER (PARTITION BY `account_phones`.`user_id` ORDER BY `account_phones`.`id` )  AS `rn` from `account_phones`) `ph2` on(((`ap`.`user_id` = `ph2`.`user_id`) and (`ph2`.`rn` = 2)))) left join (select `account_phones`.`id` AS `id`,`account_phones`.`user_id` AS `user_id`,`account_phones`.`country_code` AS `country_code`,`account_phones`.`default` AS `default`,`account_phones`.`phone` AS `phone`,`account_phones`.`name` AS `name`,`account_phones`.`created_at` AS `created_at`,`account_phones`.`updated_at` AS `updated_at`,row_number() OVER (PARTITION BY `account_phones`.`user_id` ORDER BY `account_phones`.`id` )  AS `rn` from `account_phones`) `ph3` on(((`ap`.`user_id` = `ph3`.`user_id`) and (`ph3`.`rn` = 3)))) left join `dictionnary_entries` `e1` on((`ap`.`base_id` = `e1`.`id`))) left join `dictionnary_entries` `e2` on((`ap`.`domain_id` = `e2`.`id`))) left join `dictionnary_entries` `e3` on((`ap`.`title_id` = `e3`.`id`))) left join `dictionnary_entries` `e4` on((`ap`.`profession_id` = `e4`.`id`))) left join `dictionnary_entries` `e5` on((`ap`.`savant_society_id` = `e5`.`id`))) left join `establishments` `es` on((`ap`.`establishment_id` = `es`.`id`))) left join `events_contacts` `ec` on((`ec`.`user_id` = `ap`.`user_id`))) left join `participation_types` `pt` on((`pt`.`id` = `ec`.`participation_type_id`))) left join (select `gc`.`user_id` AS `user_id`,group_concat(`g`.`name` separator ', ') AS `group_names`,concat(',',group_concat(`g`.`id` separator ','),',') AS `group_ids` from (`group_contacts` `gc` join `groups` `g` on((`gc`.`group_id` = `g`.`id`))) group by `gc`.`user_id`) `grp` on((`u`.`id` = `grp`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_contact_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_contact_view` AS select `ec`.`id` AS `id`,`e`.`id` AS `event_id`,`u`.`id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `domain`,(case when (`ap`.`account_type` = 'company') then 'Sociétés' when (`ap`.`account_type` = 'medical') then 'Professionnels de santé' when (`ap`.`account_type` = 'other') then 'Autres' end) AS `account_type_display`,`ap`.`company_name` AS `company_name`,`a`.`locality` AS `locality`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country`,json_unquote(json_extract(`de`.`name`,'$.fr')) AS `fonction`,group_concat(`g`.`name` separator ', ') AS `group`,concat(',',group_concat(`g`.`id` separator ','),',') AS `group_ids`,`ec`.`created_at` AS `created_at`,`ec`.`registration_type` AS `registration_type`,`ec`.`order_cancellation` AS `order_cancellation`,`ec`.`last_grant_id` AS `last_grant_id`,(case when ((`ec`.`is_pec_eligible` = 1) and (`ec`.`pec_enabled` = 1)) then '2' when ((`ec`.`is_pec_eligible` = 1) and (`ec`.`pec_enabled` is null)) then '1' else NULL end) AS `pec_status`,(case when ((`ec`.`is_pec_eligible` = 1) and (`ec`.`pec_enabled` = 1)) then 'pec' when ((`ec`.`is_pec_eligible` = 1) and (`ec`.`pec_enabled` is null)) then 'eligible' else NULL end) AS `pec_status_display_fr`,`pt`.`group` AS `participation_type_group`,(case when (`pt`.`group` is null) then '-' when (`pt`.`group` = 'congress') then 'Congressistes' when (`pt`.`group` = 'orator') then 'Orateurs' when (`pt`.`group` = 'industry') then 'Industriels' end) AS `participation_type_group_display`,json_unquote(json_extract(`pt`.`name`,'$.fr')) AS `participation_type`,(select count(0) from `orders` `o` where ((`o`.`event_id` = `ec`.`event_id`) and (`o`.`client_id` = `ec`.`user_id`) and (`o`.`client_type` = 'contact'))) AS `nb_orders`,(((((select count(0) from (`event_sellable_service` join `event_contact_sellable_service_choosables` on((`event_sellable_service`.`id` = `event_contact_sellable_service_choosables`.`choosable_id`))) where ((`ec`.`id` = `event_contact_sellable_service_choosables`.`event_contact_id`) and (`event_sellable_service`.`deleted_at` is null))) + (select count(0) from `event_program_session_moderators` where (`ec`.`id` = `event_program_session_moderators`.`events_contacts_id`))) + (select count(0) from `event_program_intervention_orators` where (`ec`.`id` = `event_program_intervention_orators`.`events_contacts_id`))) + (select count(0) from `event_transports` where (`ec`.`id` = `event_transports`.`events_contacts_id`))) + (select count(0) from `orders` where ((`ec`.`user_id` = `orders`.`client_id`) and (`orders`.`client_type` = 'contact') and (`orders`.`event_id` = `ec`.`event_id`)))) AS `has_something` from (((((((((((`events_contacts` `ec` join `users` `u` on((`u`.`id` = `ec`.`user_id`))) join `events` `e` on((`e`.`id` = `ec`.`event_id`))) join `account_profile` `ap` on((`u`.`id` = `ap`.`user_id`))) left join `participation_types` `pt` on((`pt`.`id` = `ec`.`participation_type_id`))) left join `dictionnary_entries` `d` on((`ap`.`domain_id` = `d`.`id`))) left join `dictionnary_entries` `de` on((`ap`.`profession_id` = `de`.`id`))) left join `main_account_address_view` `a` on((`u`.`id` = `a`.`user_id`))) left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) left join `event_group_contacts` `egc` on((`u`.`id` = `egc`.`user_id`))) left join `event_groups` `eg` on((`egc`.`event_group_id` = `eg`.`id`))) left join `groups` `g` on((`eg`.`group_id` = `g`.`id`))) where (`g`.`deleted_at` is null) group by `ec`.`id`,`event_id`,`u`.`id`,`u`.`first_name`,`u`.`last_name`,`u`.`email`,`domain`,`account_type_display`,`ap`.`company_name`,`a`.`locality`,`country`,`fonction`,`ec`.`created_at`,`participation_type_group`,`participation_type_group_display`,`participation_type` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_deposits_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_deposits_view` AS select `ed`.`id` AS `id`,`ed`.`order_id` AS `order_id`,`o`.`uuid` AS `uuid`,`ed`.`event_id` AS `event_id`,`ed`.`shoppable_type` AS `shoppable_type`,`ed`.`shoppable_label` AS `shoppable_label`,`ed`.`total_net` AS `total_net`,`ed`.`beneficiary_event_contact_id` AS `beneficiary_event_contact_id`,`ed`.`status` AS `status`,`ed`.`reimbursed_at` AS `reimbursed_at`,(case when (`oi`.`order_id` is not null) then 1 else NULL end) AS `has_invoice`,(case when ((`e`.`ends` < curdate()) and (`ec`.`is_attending` is null)) then 1 else 0 end) AS `is_attending_expired`,(`ed`.`total_net` + `ed`.`total_vat`) AS `total_ttc`,date_format(`ed`.`created_at`,'%d/%m/%Y %H:%i:%s') AS `date_fr`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `beneficiary_name` from (((((`event_deposits` `ed` left join `orders` `o` on((`ed`.`order_id` = `o`.`id`))) left join `order_invoices` `oi` on((`oi`.`order_id` = `o`.`id`))) left join `events_contacts` `ec` on((`ed`.`beneficiary_event_contact_id` = `ec`.`id`))) left join `users` `u` on((`ec`.`user_id` = `u`.`id`))) left join `events` `e` on((`ed`.`event_id` = `e`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_grant_establishments_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_grant_establishments_view` AS select `eg`.`grant_id` AS `grant_id`,`eg`.`pax` AS `pax`,`e`.`id` AS `id`,`e`.`name` AS `name`,`e`.`country_code` AS `country_code`,`e`.`type` AS `type`,`e`.`street_number` AS `street_number`,`e`.`route` AS `route`,`e`.`postal_code` AS `postal_code`,`e`.`locality` AS `locality`,`e`.`administrative_area_level_1` AS `administrative_area_level_1`,`e`.`administrative_area_level_2` AS `administrative_area_level_2`,`e`.`text_address` AS `text_address`,`e`.`lat` AS `lat`,`e`.`lon` AS `lon`,`e`.`created_at` AS `created_at`,`e`.`updated_at` AS `updated_at`,`e`.`deleted_at` AS `deleted_at`,`e`.`prefix` AS `prefix`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country` from ((`event_grant_establishments` `eg` join `establishments` `e` on((`e`.`id` = `eg`.`establishment_id`))) join `countries` `c` on((`c`.`code` = `e`.`country_code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_grant_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_grant_view` AS select `eg`.`id` AS `id`,`eg`.`event_id` AS `event_id`,json_unquote(json_extract(`eg`.`title`,'$.fr')) AS `title`,coalesce(concat(`egc`.`first_name`,' ',`egc`.`last_name`),'N/A') AS `contact`,json_unquote(json_extract(`eg`.`comment`,'$.fr')) AS `comment`,`eg`.`amount_ht` AS `amount_ht`,`eg`.`amount_ht_used` AS `amount_ht_used`,(`eg`.`amount_ht` - `eg`.`amount_ht_used`) AS `amount_ht_remaining`,`eg`.`amount_ttc` AS `amount_ttc`,`eg`.`amount_ttc_used` AS `amount_ttc_used`,(`eg`.`amount_ttc` - `eg`.`amount_ttc_used`) AS `amount_ttc_remaining`,format((`eg`.`amount_ht` / 100.0),2) AS `amount_ht_display`,format((`eg`.`amount_ht_used` / 100.0),2) AS `amount_ht_used_display`,format(((`eg`.`amount_ht` - `eg`.`amount_ht_used`) / 100.0),2) AS `amount_ht_remaining_display`,format((`eg`.`amount_ttc` / 100.0),2) AS `amount_ttc_display`,format((`eg`.`amount_ttc_used` / 100.0),2) AS `amount_ttc_used_display`,format(((`eg`.`amount_ttc` - `eg`.`amount_ttc_used`) / 100.0),2) AS `amount_ttc_remaining_display`,`eg`.`pec_fee` AS `pec_fee`,format((`eg`.`pec_fee` / 100.0),2) AS `pec_fee_display`,`eg`.`pax_avg` AS `pax_avg`,`eg`.`pax_max` AS `pax_max`,`eg`.`active` AS `active` from (`event_grant` `eg` left join `event_grant_contact` `egc` on((`eg`.`id` = `egc`.`grant_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_group_contact_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_group_contact_view` AS select `egc`.`id` AS `id`,`e`.`id` AS `event_id`,`eg`.`id` AS `event_group_id`,`eg`.`group_id` AS `group_id`,`u`.`id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`a`.`locality` AS `locality`,`ap`.`function` AS `profile_function`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country`,(case when (`u`.`id` = `eg`.`main_contact_id`) then true else false end) AS `is_main_contact`,(case when (`u`.`id` = `eg`.`main_contact_id`) then 'Oui' else 'Non' end) AS `is_main_contact_display` from ((((((`event_group_contacts` `egc` join `event_groups` `eg` on((`eg`.`id` = `egc`.`event_group_id`))) join `events` `e` on((`e`.`id` = `eg`.`event_id`))) join `users` `u` on((`u`.`id` = `egc`.`user_id`))) left join `account_profile` `ap` on((`u`.`id` = `ap`.`user_id`))) left join `main_account_address_view` `a` on((`u`.`id` = `a`.`user_id`))) left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_groups_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_groups_view` AS select `eg`.`id` AS `id`,`g`.`id` AS `group_id`,`g`.`name` AS `group_name`,`g`.`company` AS `group_company`,cast(`eg`.`created_at` as date) AS `event_group_created_at`,`eg`.`event_id` AS `event_id`,`eg`.`comment` AS `comment`,`u`.`id` AS `user_id`,concat(`u`.`first_name`,' ',`u`.`last_name`) AS `main_contact_name`,`u`.`email` AS `main_contact_email`,`p`.`phone` AS `main_contact_phone`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `main_contact_country`,(select count(distinct `ec`.`id`) from (`events_contacts` `ec` join `event_group_contacts` `egc` on((`ec`.`user_id` = `egc`.`user_id`))) where ((`egc`.`event_group_id` = `eg`.`id`) and (`ec`.`event_id` = `eg`.`event_id`))) AS `participants_count`,(select count(0) from `orders` where ((`orders`.`client_type` = 'group') and (`orders`.`client_id` = `g`.`id`))) AS `orders_count` from (((((`groups` `g` join `event_groups` `eg` on((`g`.`id` = `eg`.`group_id`))) left join `users` `u` on((`eg`.`main_contact_id` = `u`.`id`))) left join `main_account_phone_view` `p` on((`u`.`id` = `p`.`user_id`))) left join `main_account_address_view` `a` on((`u`.`id` = `a`.`user_id`))) left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) where (`g`.`deleted_at` is null) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_program_interventions_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_program_interventions_view` AS select `i`.`id` AS `id`,`i`.`is_catering` AS `is_catering`,`i`.`is_placeholder` AS `is_placeholder`,`dr`.`event_id` AS `event_id`,`i`.`event_program_session_id` AS `event_program_session_id`,concat(date_format(`dr`.`datetime_start`,'%d/%m/%Y'),' - ',`pmain`.`name`,' > ',json_unquote(json_extract(`rmain`.`name`,'$.fr'))) AS `container`,concat(date_format(min(`i`.`start`),'%Hh%i'),' - ',date_format(max(`i`.`end`),'%Hh%i')) AS `timings`,json_unquote(json_extract(`s`.`name`,'$.fr')) AS `session`,json_unquote(json_extract(`i`.`name`,'$.fr')) AS `name`,group_concat(concat(`u`.`last_name`,' ',`u`.`first_name`) order by `u`.`last_name` ASC separator ', ') AS `orators`,json_unquote(json_extract(`dictionnary_entries`.`name`,'$.fr')) AS `specificity`,`i`.`duration` AS `duration`,(case when (`i`.`is_online` = 1) then 'Oui' else 'Non' end) AS `is_online` from ((((((((`event_program_interventions` `i` join `event_program_sessions` `s` on((`i`.`event_program_session_id` = `s`.`id`))) join `event_program_day_rooms` `dr` on((`s`.`event_program_day_room_id` = `dr`.`id`))) join `place_rooms` `rmain` on((`dr`.`room_id` = `rmain`.`id`))) join `places` `pmain` on((`rmain`.`place_id` = `pmain`.`id`))) left join `dictionnary_entries` on((`i`.`specificity_id` = `dictionnary_entries`.`id`))) left join `event_program_intervention_orators` `eci` on((`eci`.`event_program_intervention_id` = `i`.`id`))) left join `events_contacts` `c` on((`c`.`id` = `eci`.`events_contacts_id`))) left join `users` `u` on((`u`.`id` = `c`.`user_id`))) group by `i`.`id`,`dr`.`datetime_start`,`i`.`start`,`i`.`end`,`s`.`name`,`i`.`name`,`dictionnary_entries`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_program_sessions_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_program_sessions_view` AS select `s`.`id` AS `id`,`s`.`is_catering` AS `is_catering`,`s`.`is_placeholder` AS `is_placeholder`,`dr`.`event_id` AS `event_id`,date_format(`dr`.`datetime_start`,'%d/%m/%Y') AS `date`,json_unquote(json_extract(`s`.`name`,'$.fr')) AS `name`,date_format(`dr`.`datetime_start`,'%d/%m/%Y') AS `datetime_start`,concat(`p`.`name`,' > ',json_unquote(json_extract(`pr`.`name`,'$.fr'))) AS `place_room`,group_concat(distinct concat(`u`.`last_name`,' ',`u`.`first_name`) order by `u`.`last_name` ASC separator ', ') AS `moderators`,concat(date_format(min(`i`.`start`),'%Hh%i'),' - ',date_format(max(`i`.`end`),'%Hh%i')) AS `timings` from (((((((`event_program_sessions` `s` join `event_program_day_rooms` `dr` on((`s`.`event_program_day_room_id` = `dr`.`id`))) left join `place_rooms` `pr` on((`pr`.`id` = `s`.`place_room_id`))) left join `places` `p` on((`p`.`id` = `pr`.`place_id`))) left join `event_program_session_moderators` `m` on((`m`.`event_program_session_id` = `s`.`id`))) left join `events_contacts` `c` on((`c`.`id` = `m`.`events_contacts_id`))) left join `users` `u` on((`u`.`id` = `c`.`user_id`))) left join `event_program_interventions` `i` on((`i`.`event_program_session_id` = `s`.`id`))) group by `s`.`id`,`dr`.`event_id`,`s`.`name`,`dr`.`datetime_start`,`pr`.`name`,`p`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_sellable_service_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_sellable_service_view` AS select `s`.`id` AS `id`,`s`.`event_id` AS `event_id`,json_unquote(json_extract(`s`.`title`,'$.fr')) AS `title_fr`,`s`.`is_invitation` AS `is_invitation`,(case when (`s`.`is_invitation` = 1) then 'Oui' else 'Non' end) AS `is_invitation_display`,json_unquote(json_extract(`sg`.`name`,'$.fr')) AS `group_fr`,date_format(`s`.`service_date`,'%d/%m/%Y') AS `service_date_fr`,`s`.`stock_initial` AS `stock_initial`,(cast(`s`.`stock_initial` as signed) - cast(`s`.`stock` as signed)) AS `reserved`,`s`.`stock` AS `stock`,`s`.`published` AS `published`,`s`.`pec_eligible` AS `pec_eligible`,group_concat(distinct cast((`esp`.`price` / 100) as unsigned) order by `esp`.`price` ASC separator ',<br>') AS `prices` from ((`event_sellable_service` `s` left join `dictionnary_entries` `sg` on((`sg`.`id` = `s`.`service_group`))) left join `event_sellable_service_prices` `esp` on((`esp`.`event_sellable_service_id` = `s`.`id`))) group by `s`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `event_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_view` AS select `e`.`id` AS `id`,`e`.`deleted_at` AS `deleted_at`,`e`.`bank_card_code` AS `codecb`,date_format(`e`.`starts`,'%d/%m/%Y') AS `starts`,date_format(`e`.`ends`,'%d/%m/%Y') AS `ends`,json_unquote(json_extract(`et`.`name`,'$.fr')) AS `name`,json_unquote(json_extract(`et`.`subname`,'$.fr')) AS `subname`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `parent`,json_unquote(json_extract(`d2`.`name`,'$.fr')) AS `type`,concat_ws(' ',`u`.`first_name`,`u`.`last_name`) AS `admin`,(case when (`e`.`published` = 1) then 'Oui' when (`e`.`published` is null) then 'Non' else 'other' end) AS `published` from ((((`events` `e` left join `events_texts` `et` on((`e`.`id` = `et`.`event_id`))) left join `users` `u` on((`e`.`admin_id` = `u`.`id`))) left join `dictionnary_entries` `d` on((`e`.`event_main_id` = `d`.`id`))) left join `dictionnary_entries` `d2` on((`e`.`event_type_id` = `d2`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `eventmanager_hotel_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `eventmanager_hotel_view` AS select `a`.`id` AS `id`,`a`.`event_id` AS `event_id`,`a`.`hotel_id` AS `hotel_id`,(select count(`bk`.`id`) from `order_cart_accommodation` `bk` where (`a`.`id` = `bk`.`event_hotel_id`)) AS `bookings`,json_unquote(json_extract(`a`.`title`,'$.fr')) AS `title`,`b`.`locality` AS `locality`,`d`.`name` AS `name`,`d`.`email` AS `email`,`d`.`phone` AS `phone`,(case when (`a`.`pec` = 1) then 'Oui' when (`a`.`pec` is null) then 'Non' end) AS `pec`,(case when (`a`.`published` is null) then 'Hors ligne' else 'En ligne' end) AS `published` from ((`event_accommodation` `a` left join `hotels` `d` on((`a`.`hotel_id` = `d`.`id`))) left join `hotel_address` `b` on((`a`.`hotel_id` = `b`.`hotel_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `front_my_orders_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `front_my_orders_view` AS select 'order' AS `type`,`o`.`event_id` AS `event_id`,`o`.`id` AS `order_id`,`o`.`uuid` AS `uuid`,`o`.`client_id` AS `client_id`,`o`.`client_type` AS `client_type`,`o`.`created_at` AS `date`,`o`.`total_net` AS `total_net`,`o`.`total_vat` AS `total_vat`,(`o`.`total_net` + `o`.`total_vat`) AS `total_ttc`,`o`.`total_pec` AS `total_pec`,`oi`.`id` AS `order_invoice_id` from (`orders` `o` left join `order_invoices` `oi` on((`oi`.`order_id` = `o`.`id`))) union all select 'refund' AS `type`,`rv`.`event_id` AS `event_id`,`rv`.`order_id` AS `order_id`,`rv`.`uuid` AS `uuid`,`rv`.`client_id` AS `client_id`,`rv`.`client_type` AS `client_type`,`rv`.`created_at_raw` AS `date`,cast((`rv`.`total_raw` / (1 + (`rv`.`vat_rate_raw` / 10000))) as signed) AS `total_net`,cast((`rv`.`total_raw` - (`rv`.`total_raw` / (1 + (`rv`.`vat_rate_raw` / 10000)))) as signed) AS `total_vat`,`rv`.`total_raw` AS `total_ttc`,0 AS `total_pec`,NULL AS `order_invoice_id` from `order_refunds_view` `rv` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `group_export_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `group_export_view` AS select `g`.`id` AS `id`,`g`.`name` AS `name`,`g`.`company` AS `company`,`g`.`billing_comment` AS `billing_comment`,`g`.`siret` AS `siret`,(case when (`a`.`billing` = 1) then 'Oui' else 'Non' end) AS `main_address_billing`,`a`.`name` AS `main_address_name`,`a`.`street_number` AS `main_address_street_number`,`a`.`route` AS `main_address_route`,`a`.`locality` AS `main_address_locality`,`a`.`postal_code` AS `main_address_postal_code`,`a`.`country_code` AS `main_address_country_code`,`a`.`text_address` AS `main_address_text_address`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `main_address_country_name` from ((`groups` `g` left join `main_group_address_view` `a` on((`a`.`group_id` = `g`.`id`))) left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `group_fullsearch_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `group_fullsearch_view` AS select `g`.`id` AS `id`,`g`.`name` AS `name`,`g`.`company` AS `company`,`g`.`billing_comment` AS `billing_comment`,`g`.`siret` AS `siret`,`g`.`created_by` AS `created_by`,`g`.`vat_id` AS `vat_id`,`v`.`rate` AS `vat_rate`,(case when (`a`.`billing` = 1) then 'Oui' else 'Non' end) AS `main_address_billing`,`a`.`name` AS `main_address_name`,`a`.`street_number` AS `main_address_street_number`,`a`.`route` AS `main_address_route`,`a`.`locality` AS `main_address_locality`,`a`.`postal_code` AS `main_address_postal_code`,`a`.`country_code` AS `main_address_country_code`,`a`.`text_address` AS `main_address_text_address`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `main_address_country_name`,`uc`.`id` AS `creator_user_id`,`uc`.`first_name` AS `creator_first_name`,`uc`.`last_name` AS `creator_last_name`,`uc`.`email` AS `creator_email`,`apc`.`account_type` AS `creator_account_type`,`apc`.`base_id` AS `creator_base_id`,`apc`.`domain_id` AS `creator_domain_id`,`apc`.`title_id` AS `creator_title_id`,`apc`.`profession_id` AS `creator_profession_id`,`apc`.`language_id` AS `creator_language_id`,`apc`.`savant_society_id` AS `creator_savant_society_id`,json_unquote(json_extract(`ec1`.`name`,'$.fr')) AS `creator_base`,json_unquote(json_extract(`ec2`.`name`,'$.fr')) AS `creator_domain`,json_unquote(json_extract(`ec3`.`name`,'$.fr')) AS `creator_title`,json_unquote(json_extract(`ec4`.`name`,'$.fr')) AS `creator_profession`,json_unquote(json_extract(`ec5`.`name`,'$.fr')) AS `creator_savant_society`,json_unquote(json_extract(`ec6`.`name`,'$.fr')) AS `creator_language`,`apc`.`civ` AS `creator_civ`,`apc`.`birth` AS `creator_birth`,`apc`.`cotisation_year` AS `creator_cotisation_year`,`apc`.`blacklisted` AS `creator_blacklisted`,`apc`.`blacklist_comment` AS `creator_blacklist_comment`,`apc`.`notes` AS `creator_notes`,`apc`.`function` AS `creator_function`,`apc`.`rpps` AS `creator_rpps` from (((((((((((`groups` `g` left join `main_group_address_view` `a` on((`a`.`group_id` = `g`.`id`))) left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) left join `vat` `v` on((`v`.`id` = `g`.`vat_id`))) left join `account_profile` `apc` on((`apc`.`user_id` = `g`.`created_by`))) left join `users` `uc` on((`apc`.`user_id` = `uc`.`id`))) left join `dictionnary_entries` `ec1` on((`apc`.`base_id` = `ec1`.`id`))) left join `dictionnary_entries` `ec2` on((`apc`.`domain_id` = `ec2`.`id`))) left join `dictionnary_entries` `ec3` on((`apc`.`title_id` = `ec3`.`id`))) left join `dictionnary_entries` `ec4` on((`apc`.`profession_id` = `ec4`.`id`))) left join `dictionnary_entries` `ec5` on((`apc`.`savant_society_id` = `ec5`.`id`))) left join `dictionnary_entries` `ec6` on((`apc`.`language_id` = `ec6`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `group_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `group_view` AS select `g`.`id` AS `id`,`g`.`name` AS `name`,`g`.`company` AS `company`,`g`.`phone` AS `phone`,`g`.`deleted_at` AS `deleted_at`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country` from ((`groups` `g` left join `main_group_address_view` `a` on((`a`.`group_id` = `g`.`id`))) left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `hotel_history_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `hotel_history_view` AS select `a`.`hotel_id` AS `hotel_id`,`b`.`name` AS `hotel`,date_format(`c`.`starts`,'%d/%m/%Y') AS `event_starts`,date_format(`c`.`ends`,'%d/%m/%Y') AS `event_ends`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `event`,`e`.`locality` AS `locality`,json_unquote(json_extract(`f`.`name`,'$.fr')) AS `country` from (((((`event_accommodation` `a` join `hotels` `b` on((`a`.`hotel_id` = `b`.`id`))) join `events` `c` on((`a`.`event_id` = `c`.`id`))) join `events_texts` `d` on((`a`.`event_id` = `d`.`event_id`))) left join `hotel_address` `e` on((`a`.`hotel_id` = `e`.`hotel_id`))) left join `countries` `f` on((`e`.`country_code` = `f`.`code`))) order by `c`.`starts` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `hotel_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `hotel_view` AS select `a`.`id` AS `id`,`a`.`name` AS `name`,`a`.`email` AS `email`,`a`.`phone` AS `phone`,`b`.`locality` AS `locality`,json_unquote(json_extract(`a`.`description`,'$.fr')) AS `description`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country` from ((`hotels` `a` left join `hotel_address` `b` on((`a`.`id` = `b`.`hotel_id`))) left join `countries` `c` on((`b`.`country_code` = `c`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `main_account_address_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `main_account_address_view` AS with `prioritizedaddresses` as (select `account_address`.`id` AS `id`,`account_address`.`user_id` AS `user_id`,`account_address`.`billing` AS `billing`,`account_address`.`street_number` AS `street_number`,`account_address`.`route` AS `route`,`account_address`.`locality` AS `locality`,`account_address`.`postal_code` AS `postal_code`,`account_address`.`country_code` AS `country_code`,`account_address`.`text_address` AS `text_address`,`account_address`.`lat` AS `lat`,`account_address`.`lon` AS `lon`,`account_address`.`created_at` AS `created_at`,`account_address`.`updated_at` AS `updated_at`,`account_address`.`name` AS `name`,`account_address`.`company` AS `company`,`account_address`.`complementary` AS `complementary`,`account_address`.`cedex` AS `cedex`,(case when (`account_address`.`company` is not null) then 1 when (`account_address`.`billing` = 1) then 2 else 3 end) AS `priority` from `account_address`) select `prioritizedaddresses`.`id` AS `id`,`prioritizedaddresses`.`user_id` AS `user_id`,`prioritizedaddresses`.`billing` AS `billing`,`prioritizedaddresses`.`street_number` AS `street_number`,`prioritizedaddresses`.`route` AS `route`,`prioritizedaddresses`.`locality` AS `locality`,`prioritizedaddresses`.`postal_code` AS `postal_code`,`prioritizedaddresses`.`country_code` AS `country_code`,`prioritizedaddresses`.`text_address` AS `text_address`,`prioritizedaddresses`.`lat` AS `lat`,`prioritizedaddresses`.`lon` AS `lon`,`prioritizedaddresses`.`created_at` AS `created_at`,`prioritizedaddresses`.`updated_at` AS `updated_at`,`prioritizedaddresses`.`name` AS `name`,`prioritizedaddresses`.`company` AS `company`,`prioritizedaddresses`.`complementary` AS `complementary`,`prioritizedaddresses`.`cedex` AS `cedex`,`prioritizedaddresses`.`priority` AS `priority` from `prioritizedaddresses` where (`prioritizedaddresses`.`user_id`,`prioritizedaddresses`.`priority`,`prioritizedaddresses`.`id`) in (select `prioritizedaddresses`.`user_id`,min(`prioritizedaddresses`.`priority`) AS `min_priority`,min(`prioritizedaddresses`.`id`) AS `min_id` from `prioritizedaddresses` group by `prioritizedaddresses`.`user_id`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `main_account_phone_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `main_account_phone_view` AS with `prioritizedphones` as (select `account_phones`.`id` AS `id`,`account_phones`.`user_id` AS `user_id`,`account_phones`.`country_code` AS `country_code`,`account_phones`.`default` AS `default`,`account_phones`.`phone` AS `phone`,`account_phones`.`name` AS `name`,`account_phones`.`created_at` AS `created_at`,`account_phones`.`updated_at` AS `updated_at`,(case when (`account_phones`.`default` is not null) then 1 else 2 end) AS `priority` from `account_phones`) select `prioritizedphones`.`id` AS `id`,`prioritizedphones`.`user_id` AS `user_id`,`prioritizedphones`.`country_code` AS `country_code`,`prioritizedphones`.`default` AS `default`,`prioritizedphones`.`phone` AS `phone`,`prioritizedphones`.`name` AS `name`,`prioritizedphones`.`created_at` AS `created_at`,`prioritizedphones`.`updated_at` AS `updated_at`,`prioritizedphones`.`priority` AS `priority` from `prioritizedphones` where (`prioritizedphones`.`user_id`,`prioritizedphones`.`priority`,`prioritizedphones`.`id`) in (select `prioritizedphones`.`user_id`,min(`prioritizedphones`.`priority`) AS `min_priority`,min(`prioritizedphones`.`id`) AS `min_id` from `prioritizedphones` group by `prioritizedphones`.`user_id`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `main_group_address_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `main_group_address_view` AS with `prioritizedaddresses` as (select `group_address`.`id` AS `id`,`group_address`.`group_id` AS `group_id`,`group_address`.`billing` AS `billing`,`group_address`.`name` AS `name`,`group_address`.`street_number` AS `street_number`,`group_address`.`route` AS `route`,`group_address`.`locality` AS `locality`,`group_address`.`postal_code` AS `postal_code`,`group_address`.`country_code` AS `country_code`,`group_address`.`text_address` AS `text_address`,`group_address`.`lat` AS `lat`,`group_address`.`lon` AS `lon`,`group_address`.`created_at` AS `created_at`,`group_address`.`updated_at` AS `updated_at`,`group_address`.`complementary` AS `complementary`,`group_address`.`cedex` AS `cedex`,(case when (`group_address`.`billing` = 1) then 1 else 2 end) AS `priority` from `group_address`) select `prioritizedaddresses`.`id` AS `id`,`prioritizedaddresses`.`group_id` AS `group_id`,`prioritizedaddresses`.`billing` AS `billing`,`prioritizedaddresses`.`name` AS `name`,`prioritizedaddresses`.`street_number` AS `street_number`,`prioritizedaddresses`.`route` AS `route`,`prioritizedaddresses`.`locality` AS `locality`,`prioritizedaddresses`.`postal_code` AS `postal_code`,`prioritizedaddresses`.`country_code` AS `country_code`,`prioritizedaddresses`.`text_address` AS `text_address`,`prioritizedaddresses`.`lat` AS `lat`,`prioritizedaddresses`.`lon` AS `lon`,`prioritizedaddresses`.`created_at` AS `created_at`,`prioritizedaddresses`.`updated_at` AS `updated_at`,`prioritizedaddresses`.`complementary` AS `complementary`,`prioritizedaddresses`.`cedex` AS `cedex`,`prioritizedaddresses`.`priority` AS `priority` from `prioritizedaddresses` where (`prioritizedaddresses`.`group_id`,`prioritizedaddresses`.`priority`,`prioritizedaddresses`.`id`) in (select `prioritizedaddresses`.`group_id`,min(`prioritizedaddresses`.`priority`) AS `min_priority`,min(`prioritizedaddresses`.`id`) AS `min_id` from `prioritizedaddresses` group by `prioritizedaddresses`.`group_id`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `order_invoices_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `order_invoices_view` AS select `b`.`id` AS `id`,`b`.`invoice_number` AS `invoice_number`,date_format(`b`.`created_at`,'%d/%m/%Y %H:%i') AS `created_at`,`a`.`id` AS `order_id`,`a`.`event_id` AS `event_id`,`a`.`uuid` AS `uuid`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `client_name`,format(((`a`.`total_vat` + `a`.`total_net`) / 100),2) AS `total`,format((((`a`.`total_vat` + `a`.`total_net`) - `a`.`total_pec`) / 100),2) AS `total_paid` from ((`order_invoices` `b` join `orders` `a` on((`a`.`id` = `b`.`order_id`))) join `order_invoiceable` `c` on((`a`.`id` = `c`.`order_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `order_payments_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `order_payments_view` AS select `b`.`id` AS `id`,`d`.`invoice_number` AS `invoice_number`,`d`.`id` AS `invoice_id`,`a`.`id` AS `order_id`,`a`.`event_id` AS `event_id`,`a`.`uuid` AS `uuid`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `payer`,format((`b`.`amount` / 100),2) AS `amount`,date_format(`b`.`date`,'%d/%m/%Y') AS `date`,`b`.`authorization_number` AS `authorization_number`,`b`.`payment_method` AS `payment_method`,(case when (`b`.`payment_method` = 'cb_paybox') then 'CB (Paybox)' when (`b`.`payment_method` = 'cb_vad') then 'CB (VAD)' when (`b`.`payment_method` = 'check') then 'Chèque' when (`b`.`payment_method` = 'bank_transfer') then 'Virement' when (`b`.`payment_method` = 'cash') then 'Espèces' else 'Non spécifié' end) AS `payment_method_translated`,`b`.`bank` AS `bank`,`b`.`issuer` AS `issuer`,`b`.`check_number` AS `check_number`,`b`.`card_number` AS `card_number` from (((`order_payments` `b` join `orders` `a` on((`a`.`id` = `b`.`order_id`))) join `order_invoiceable` `c` on((`a`.`id` = `c`.`order_id`))) left join `order_invoices` `d` on((`d`.`order_id` = `a`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `order_refunds_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `order_refunds_view` AS select `b`.`id` AS `id`,`b`.`refund_number` AS `refund_number`,`b`.`uuid` AS `uuid`,date_format(`b`.`created_at`,'%d/%m/%Y %H:%i') AS `created_at`,`b`.`created_at` AS `created_at_raw`,`b`.`order_id` AS `order_id`,`a`.`event_id` AS `event_id`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `client_name`,`c`.`account_id` AS `client_id`,`c`.`account_type` AS `client_type`,format((sum(`d`.`amount`) / 100),2) AS `total`,sum(`d`.`amount`) AS `total_raw`,(`e`.`rate` / 100) AS `vat_rate`,`e`.`rate` AS `vat_rate_raw` from ((((`order_refunds` `b` join `orders` `a` on((`a`.`id` = `b`.`order_id`))) join `order_invoiceable` `c` on((`a`.`id` = `c`.`order_id`))) join `order_refunds_items` `d` on((`b`.`id` = `d`.`refund_id`))) join `vat` `e` on((`e`.`id` = `d`.`vat_id`))) group by `b`.`id`,`b`.`refund_number`,`b`.`uuid`,`b`.`created_at`,`b`.`order_id`,`a`.`event_id`,`c`.`first_name`,`c`.`last_name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `orders_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `orders_view` AS select `o`.`id` AS `id`,`o`.`event_id` AS `event_id`,`o`.`created_at` AS `date`,`o`.`client_type` AS `client_type`,`o`.`origin` AS `origin`,`o`.`marker` AS `marker`,(case when ('group' = `o`.`client_type`) then 'Groupe' when ('contact' = `o`.`client_type`) then 'Participant' end) AS `client_type_display`,`o`.`client_id` AS `client_id`,`o`.`status` AS `status`,`o`.`paybox_num_trans` AS `paybox_num_trans`,(case when ('paid' = `o`.`status`) then 'Soldée' when ('unpaid' = `o`.`status`) then 'Non-soldée' end) AS `status_display`,`oi`.`invoice_number` AS `invoice_number`,(case when ('group' = `o`.`client_type`) then `g`.`name` when ('contact' = `o`.`client_type`) then concat(`u`.`last_name`,' ',`u`.`first_name`) end) AS `name`,format(((`o`.`total_net` + `o`.`total_vat`) / 100),2) AS `total`,format((coalesce(`p`.`payments_total`,0) / 100),2) AS `payments_total`,format((`o`.`total_pec` / 100),2) AS `total_pec`,`ec`.`order_cancellation` AS `order_cancellation`,(case when (`oi`.`order_id` is not null) then 1 else NULL end) AS `has_invoice`,(case when (`oi`.`order_id` is not null) then 'Oui' else 'Non' end) AS `has_invoice_display` from ((((((`orders` `o` left join `order_invoices` `oi` on((`oi`.`order_id` = `o`.`id`))) left join `front_transactions` `ft` on((`ft`.`order_uuid` = `o`.`uuid`))) left join `groups` `g` on(((`o`.`client_type` = 'group') and (`g`.`id` = `o`.`client_id`)))) left join `users` `u` on(((`o`.`client_type` = 'contact') and (`u`.`id` = `o`.`client_id`)))) left join (select `op`.`order_id` AS `order_id`,sum(`op`.`amount`) AS `payments_total` from `order_payments` `op` group by `op`.`order_id`) `p` on((`p`.`order_id` = `o`.`id`))) left join `events_contacts` `ec` on(((`o`.`client_type` = 'contact') and (`u`.`id` = `ec`.`user_id`) and (`o`.`event_id` = `ec`.`event_id`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `place_room_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `place_room_view` AS select `place_rooms`.`id` AS `id`,`place_rooms`.`place_id` AS `place_id`,json_unquote(json_extract(`place_rooms`.`name`,'$.fr')) AS `name`,json_unquote(json_extract(`place_rooms`.`level`,'$.fr')) AS `level` from `place_rooms` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `place_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `place_view` AS select `p`.`id` AS `id`,`p`.`name` AS `name`,`p`.`email` AS `email`,`p`.`phone` AS `phone`,`pa`.`locality` AS `locality`,json_unquote(json_extract(`dpt`.`name`,'$.fr')) AS `type`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country` from (((`places` `p` left join `dictionnary_entries` `dpt` on((`dpt`.`id` = `p`.`place_type_id`))) left join `place_addresses` `pa` on((`p`.`id` = `pa`.`place_id`))) left join `countries` `c` on((`pa`.`country_code` = `c`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `sellable_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `sellable_view` AS select `a`.`id` AS `id`,`a`.`deleted_at` AS `deleted_at`,json_unquote(json_extract(`a`.`title`,'$.fr')) AS `title_fr`,json_unquote(json_extract(`a`.`title`,'$.en')) AS `title_en`,concat(format((`a`.`price` / 100),2),' €') AS `price`,(case when (`a`.`sold_per` = 'unit') then 'Unité' else 'day' end) AS `sold_per`,(case when (`a`.`published` = 1) then 'Oui' when (`a`.`published` is null) then 'Non' end) AS `published`,json_unquote(json_extract(`b`.`name`,'$.fr')) AS `category_fr`,json_unquote(json_extract(`b`.`name`,'$.en')) AS `category_en` from (`sellables` `a` left join `dictionnary_entries` `b` on((`b`.`id` = `a`.`category_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `user_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `user_view` AS select `u`.`id` AS `id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`deleted_at` AS `deleted_at`,json_unquote(json_extract(`d`.`name`,'$.fr')) AS `domain`,`ap`.`account_type` AS `participation`,`ap`.`company_name` AS `company_name`,`a`.`locality` AS `locality`,json_unquote(json_extract(`c`.`name`,'$.fr')) AS `country`,json_unquote(json_extract(`de`.`name`,'$.fr')) AS `fonction`,group_concat(`g`.`name` separator ', ') AS `group`,concat(',',group_concat(`g`.`id` separator ','),',') AS `group_ids` from (((((((`users` `u` join `account_profile` `ap` on((`u`.`id` = `ap`.`user_id`))) left join `dictionnary_entries` `d` on((`ap`.`domain_id` = `d`.`id`))) left join `dictionnary_entries` `de` on((`ap`.`profession_id` = `de`.`id`))) left join `main_account_address_view` `a` on((`u`.`id` = `a`.`user_id`))) left join `countries` `c` on((`a`.`country_code` = `c`.`code`))) left join `group_contacts` `gc` on((`u`.`id` = `gc`.`user_id`))) left join `groups` `g` on((`gc`.`group_id` = `g`.`id`))) group by `u`.`id`,`u`.`first_name`,`u`.`last_name`,`u`.`email`,`domain`,`participation`,`ap`.`company_name`,`a`.`locality`,`country`,`fonction` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `users_administrateurs_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `users_administrateurs_view` AS select `users`.`id` AS `id`,concat(`users`.`first_name`,' ',`users`.`last_name`) AS `name`,`users`.`email` AS `email`,`users_profile`.`mobile` AS `mobile`,`users`.`deleted_at` AS `deleted_at` from (`users` join `users_profile` on((`users`.`id` = `users_profile`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2014_10_12_200000_add_two_factor_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2022_05_15_201324_create_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2022_05_15_205225_meta',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2022_05_15_214516_create_user_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2022_06_19_003548_create_mediaclass',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2022_08_11_100540_create_nav',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2022_08_30_154145_create_users_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2022_08_30_161423_create_users_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2022_08_30_162932_create_site_owner',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2022_12_02_094052_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2023_01_19_103108_create_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2023_01_31_154836_create_table_dictionnaires',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2023_01_31_183115_create_dictionnaries_entries',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2023_02_02_160122_modify_mediaclass',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2023_02_06_121355_create_hotels_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2023_02_06_134509_create_users_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2023_02_06_165639_create_hotel_address_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2023_02_07_155147_create_establishments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2023_02_08_171422_alter_establishments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2023_02_10_140028_create_places_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2023_02_21_144515_create_place_addresses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2023_02_21_182324_create_place_rooms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2023_02_22_130520_create_account_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2023_02_22_150513_create_account_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2023_02_22_150538_drop_clients_profile_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2023_02_27_104714_add_user_type_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2023_03_02_123645_create_account_phones',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2023_03_02_152707_add_fields_to_account_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2023_03_02_154226_add_key_to_establishments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2023_03_03_154647_add_fields_to_account_phones',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2023_03_03_195024_reassing_default_billing_address_to_account_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2023_03_06_152734_add_rpps_to_account_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2023_03_06_164507_create_group_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2023_03_07_103514_change_fields_in_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2023_03_09_200802_create_group_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2023_03_10_195054_create_account_mails_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2023_03_17_173839_modify_account_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2023_03_17_204033_add_establishment_to_account_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2023_03_20_172338_create_account_documents',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2023_03_20_215953_create_account_cards',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2023_03_30_095941_add_prefix_to_establishments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2023_03_30_115912_change_country_to_country_code_in_establishments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2023_04_03_102953_create_bank_accounts',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2023_04_04_081602_create_table_vat',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2023_04_05_160253_create_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2023_04_05_170842_create_events_texts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2023_04_05_173042_create_events_shops_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2023_04_05_175726_create_events_pec_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2023_04_11_142141_create_events_contacts',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2023_04_17_113809_add_column_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2023_04_17_170721_add_column_to_events_shops',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2023_04_18_185200_reset_place_address_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2023_04_21_091344_add_serialized_config_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2023_04_21_100013_create_event_orator_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2023_04_21_103312_create_event_profession_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2023_04_21_103910_create_event_participation',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2023_04_21_113816_create_event_domain',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2023_04_21_114151_create_event_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2023_04_21_121726_create_event_transport',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2023_04_21_223143_create_event_shoprange',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2023_04_22_003716_create_event_shopdocs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2023_04_23_192754_add_subgroup_to_mediaclass',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2023_04_25_155813_create_mailtemplates',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2023_04_28_140811_create_sellables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2023_05_02_140811_create_sellables_by_shop',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2023_05_23_175636_create_event_accommodation',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2023_05_23_181101_create_event_accommodation_deposit',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2023_05_23_181358_create_event_accommodation_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2023_05_29_113608_create_event_accommodation_room_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2023_05_29_113935_create_event_accommodation_room',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2023_06_01_132636_create_event_accommodation_contingent',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2023_06_01_135935_create_event_accommodation_contingent_config',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2023_06_06_103433_create_participation_types',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2023_06_07_161413_create_event_accommodation_blocked_room',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2023_06_15_153206_create_event_accommodation_grant',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2023_07_18_111636_add_columns_to_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2023_07_18_143202_remove_column_from_events_pec_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2023_07_20_095638_add_company_to_account_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2023_07_20_135627_create_event_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2023_07_21_104032_create_custom_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2023_07_21_104033_create_custom_fields_modules',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2023_07_21_104034_create_custom_fields_modules_data',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2023_07_21_113440_create_custom_fields_content',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2023_07_28_172101_create_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2023_07_28_175124_create_event_sellable_service_ptypes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2023_07_28_175248_create_event_sellable_service_professions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2023_08_16_155126_create_event_sellable_service_options',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2023_08_17_103016_create_event_sellable_service_prices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2023_08_17_183016_create_event_sellable_service_deposits',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2023_08_18_095957_create_event_pec_participations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2023_08_18_101731_create_event_pec_domains',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2023_08_18_192726_create_event_grant_deposit',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2023_08_18_192728_create_event_grant_participation',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2023_08_18_192741_create_event_grant_location',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2023_08_24_145030_create_event_sellable_service_choosables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2023_08_25_150040_create_event_grant',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2023_08_25_155913_create_event_grant_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2023_08_25_160257_create_event_grant_contact',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2023_08_25_165132_create_event_grant_accommodation_dates',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2023_08_25_170021_create_event_grant_participation',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2023_08_25_170027_create_event_grant_location',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2023_08_25_170036_create_event_grant_profession',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2023_08_25_170048_create_event_grant_domain',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2023_09_08_165607_create_event_program_days_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2023_09_08_165620_create_event_program_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2023_09_08_165630_create_event_program_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2023_09_08_224328_add_has_program_to_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2023_09_20_140551_create_event_transports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2023_09_21_113537_add_amount_to_event_grant_location',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2023_09_25_160839_add_transfert_participants_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2023_09_27_140331_add_participation_type_id_to_events_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2023_09_27_141045_create_event_contact_transports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2023_09_27_143924_create_event_contact_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2023_09_27_170508_create_countries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2023_09_27_170509_account_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2023_09_28_172810_change_country_to_contry_code_in_hotel_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2023_09_28_174435_create_hotel_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2023_09_28_182833_create_event_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2023_09_29_141153_place_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2023_09_29_141817_create_event_grant_establishments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2023_09_29_145234_establishments_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2023_09_29_152051_groups_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2023_09_29_153625_eventmanager_hotel_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2023_09_29_175448_update_account_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2023_09_29_181340_change_title_id_to_nullable_in_account_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2023_10_02_172044_hotel_history_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2023_10_02_180714_update_eventmanager_hotel_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2023_10_03_095353_create_users_administrateurs_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2023_10_03_105116_add_place_room_id_to_event_program_sessions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2023_10_05_160936_add_notes_col_to_hotel_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2023_10_05_210914_add_place_room_id_if_not_exists_to_event_program_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2023_10_06_154806_add_administrative_area_level_1_short_to_hotel_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2023_10_06_171702_modify_transfert_related_columns_in_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2023_10_09_113036_add_language_id_to_account_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2023_10_09_163844_update_account_view_for_only_full_group_by',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2023_10_10_174120_add_participation_group_col_to_events_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2023_10_11_094029_create_event_contact_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2023_10_11_122015_modify_pax_column_on_event_grant_establishments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2023_10_11_171054_create_events_contacts_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2023_10_11_171105_create_events_contacts_orders_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2023_10_12_173726_create_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2023_10_12_173727_add_event_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2023_10_12_173728_add_batch_uuid_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2023_10_13_152051_update_groups_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2023_10_13_175346_create_event_grant_allocations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2023_10_16_114233_update_hotel_history_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2023_10_16_145947_add_fields_to_events_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2023_10_16_171218_update_events_contacts_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2023_10_17_104919_add_type_col_to_places',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2023_10_17_112046_update_place_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (155,'2023_10_17_155754_create_place_room_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2023_10_18_112831_create_main_account_address_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (157,'2023_10_18_112832_create_main_account_phone_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (158,'2023_10_18_112833_update_account_view_only_one_row_per_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (159,'2023_10_20_103035_create_account_profile_export_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (160,'2023_10_20_112951_remove_place_id_from_event_program_sessions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (161,'2023_10_20_114605_remove_place_id_from_event_program_interventions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (162,'2023_10_20_115411_add_start_and_end_to_event_program_interventions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (163,'2023_10_20_160048_create_sellable_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2023_10_20_174225_update_sellable_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2023_10_23_115439_add_comission_to_hotels',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2023_10_23_142956_update_event_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2023_10_23_145916_create_newsletter_lists_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2023_10_23_145947_create_newsletter_list_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2023_10_24_110340_remove_description_public_from_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2023_10_24_115207_modify_stock_in_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2023_10_24_153237_create_place_rooms_setup',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (172,'2023_10_24_153620_remove_place_setup_columns_from_place_rooms',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2023_10_24_174952_create_account_full_search_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2023_10_25_114818_modify_column_names_in_event_shoprange',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2023_10_26_132910_create_saved_searches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2023_10_26_170125_update_users_administrateurs_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (177,'2023_10_27_164820_alter_pax_columns_in_event_grant',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2023_10_30_095140_make_expires_at_nullable_in_account_cards',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (179,'2023_10_30_135700_replace_comission_from_hotels_to_event_hotels',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (180,'2023_10_30_182239_create_orders',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (181,'2023_10_30_182245_create_order_payer',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (182,'2023_10_31_152451_create_event_program_interventions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (183,'2023_10_31_170144_add_ask_video_distribution_authorization_col_to_event_program_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (184,'2023_11_02_095224_add_order_payer_col_to_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (185,'2023_11_02_095224_create_order_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2023_11_02_095225_create_order_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2023_11_02_112044_create_order_payments_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (188,'2023_11_02_140205_create_order_invoices_cancels_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (189,'2023_11_02_151451_remove_starts_from_event_sellable_service_prices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2023_11_02_175828_create_order_invoices_cancels_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (191,'2023_11_03_141737_make_invoice_id_col_nullable_on_order_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (192,'2023_11_03_172848_add_order_id_col_to_order_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (193,'2023_11_06_153301_add_comment_and_price_to_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (194,'2023_11_07_111158_add_ask_video_authorization_to_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2023_11_07_112025_add_commission_columns_to_event_accommodation',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (196,'2023_11_07_112209_remove_ask_video_distribution_authorization_col_from_event_program_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (197,'2023_11_07_150800_add_website_to_hotels',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (198,'2023_11_07_152929_make_description_nullable_in_place_rooms_setup',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (199,'2023_11_08_133646_add_name_col_to_order_invoices_cancels_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (200,'2023_11_08_161038_create_main_group_address_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (201,'2023_11_08_161526_update_groups_view_one_row_per_group',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (202,'2023_11_09_094650_add_main_contact_col_in_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (203,'2023_11_09_094655_create_group_export_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (204,'2023_11_09_133812_create_group_fullsearch_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (205,'2023_11_09_165814_remove_cols_from_event_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (206,'2023_11_09_170031_add_timestamps_to_event_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2023_11_09_170035_add_fields_to_event_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2023_11_09_170041_create_event_groups_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2023_11_10_152538_update_events_contacts_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (210,'2023_11_10_170606_remove_created_at_col_from_events_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (211,'2023_11_10_171024_add_timestamps_to_events_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (212,'2023_11_13_152743_add_vat_id_to_event_accommodation',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (213,'2023_11_13_170752_create_event_contact_fullsearch_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (214,'2023_11_14_142340_update_enum_in_saved_searches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (215,'2023_11_14_152804_update_event_groups_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (216,'2023_11_14_161837_update_event_groups_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (217,'2023_11_14_161959_alter_orders_1_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2023_11_14_161959_alter_orders_2_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (219,'2023_11_14_171500_create_order_cart_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (220,'2023_11_15_144551_drop_vat_id_from_orders',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (221,'2023_11_15_152516_create_accommodation_cart',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (222,'2023_11_15_161153_create_service_cart',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (223,'2023_11_15_164508_add_subname_col_to_event_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (224,'2023_11_16_121201_rename_event_program_days_to_event_program_day_rooms',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2023_11_16_130212_alter_accompanying_in_accommodation_cart',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2023_11_16_145625_add_service_id_to_service_cart',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (227,'2023_11_16_151913_rename_cart_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (228,'2023_11_17_095345_rename_shopable_in_order_cart',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (229,'2023_11_17_103430_update_event_program_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (230,'2023_11_17_105712_create_event_program_sessions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (231,'2023_11_17_113046_update_event_program_interventions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (232,'2023_11_20_112221_drop_order_payments_invoices_views',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (233,'2023_11_20_144515_remove_event_program_day_id_from_event_program_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (234,'2023_11_20_151203_create_order_invoiceable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (235,'2023_11_20_181550_add_company_to_order_invoiceable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (236,'2023_11_20_182758_add_order_id_to_order_cart',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (237,'2023_11_20_183150_remove_vat_id_from_order_cart',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (238,'2023_11_21_094144_add_event_id_col_to_events_contacts_transports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (239,'2023_11_21_112701_drop_event_program_day_room_id_from_even_program_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (240,'2023_11_21_114605_add_event_program_day_room_id_to_even_program_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (241,'2023_11_21_135241_alter_service_cart_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (242,'2023_11_21_144932_add_event_type_col_to_event_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (243,'2023_11_21_153853_add_service_id_to_order_cart_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (244,'2023_11_21_214931_add_department_to_invoiceable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (245,'2023_11_22_090845_create_order_temp_stock_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (246,'2023_11_28_092959_update_foreign_key_in_event_participation_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (247,'2023_11_28_140738_make_base_id_nullable_in_account_profile_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (248,'2023_11_28_160447_add_date_to_order_temp_stock_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (249,'2023_11_29_142417_create_order_notes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (250,'2023_12_06_170941_alter_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (251,'2023_12_06_175853_alter_first_name_in_order_invoiceable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (252,'2023_12_08_182009_create_order_cart_service_attributions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (253,'2023_12_12_152821_create_event_front_config_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (254,'2023_12_12_162221_add_date_to_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (255,'2023_12_15_145626_add_cols_to_event_transports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (256,'2023_12_15_145819_remove_events_contacts_transports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (257,'2023_12_15_162212_add_service_id_to_order_cart_service_attributions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (258,'2023_12_15_191635_add_date_and_created_at_to_order_cart_service_attributions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (259,'2023_12_16_073910_create_temporary_mails_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (260,'2023_12_17_203540_add_account_type_to_order_invoiceable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (261,'2023_12_19_174334_add_service_group_combined_to_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (262,'2023_12_21_114705_update_event_program_interventions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (263,'2023_12_21_134805_remove_status_from_event_program_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (264,'2023_12_21_135205_add_status_to_event_contact_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (265,'2023_12_21_194010_nullable_fields_in_order_invoiceable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (266,'2023_12_21_203808_update_specificity_id_in_event_program_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (267,'2023_12_22_062546_remove_fields_from_event_program_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (268,'2023_12_22_064328_add_cols_to_event_contact_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (269,'2023_12_22_074623_update_event_program_sessions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (270,'2023_12_22_101502_add_unlimited_to_events_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (271,'2023_12_22_143947_add_cedex_to_account_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (272,'2023_12_22_153928_add_address_id_to_order_invoiceable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (273,'2023_12_24_161739_add_column_to_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (274,'2023_12_24_163341_remove_limited_to_one_from_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (275,'2023_12_24_184643_add_fields_to_group_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (276,'2023_12_24_202832_create_temporary_uploads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (277,'2023_12_24_203449_create_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (278,'2023_12_29_061905_update_event_program_interventions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (279,'2023_12_30_040905_remove_place_room_id_from_event_program_interventions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (280,'2023_12_30_041409_update_event_program_interventions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (281,'2024_01_05_174003_add_stock_initial_to_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (282,'2024_01_05_185953_add_deleted_at_to_event_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (283,'2024_01_08_142234_create_events_contacts_moderators_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (284,'2024_01_08_173657_update_event_program_sessions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (285,'2024_01_09_165655_recreate_events_contacts_moderators_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (286,'2024_01_09_170924_recreate_events_contacts_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (287,'2024_01_10_093912_rename_events_contacts_interventions_to_event_program_intervention_orators',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (288,'2024_01_10_093915_drop_events_contacts_moderators_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (289,'2024_01_10_093917_create_event_program_session_moderators_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (290,'2024_01_10_100457_update_event_program_interventions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (291,'2024_01_10_100458_update_event_program_sessions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (292,'2024_01_10_144422_relocate_date_liimitation_event_sellables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (293,'2024_01_10_151022_make_event_main_id_nullable_in_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (294,'2024_01_10_151125_add_deleted_at_to_dictionnary_entries',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (295,'2024_01_10_153300_make_profession_id_nullable_in_account_profile_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (296,'2024_01_10_172232_create_events_clients',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (297,'2024_01_11_101527_event_accommodation_blocked_group_room',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (298,'2024_01_12_093047_update_events_contacts_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (299,'2024_01_12_165539_drop_status_from_event_program_intervention_orators_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (300,'2024_01_12_165540_add_status_col_to_event_program_intervention_orators_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (301,'2024_01_12_171223_drop_status_from_event_program_session_moderators_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (302,'2024_01_12_171224_add_status__col_to_event_program_session_moderators_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (303,'2024_01_15_120130_alter_column_group_id_in_event_accommodation_blocked_group_room',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (304,'2024_01_15_160140_create_user_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (305,'2024_01_16_152027_update_events_texts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (306,'2024_01_17_133355_relocation_order_accompanying',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (307,'2024_01_17_135010_create_order_accompanying',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (308,'2024_01_22_164029_add_request_completed_col_to_event_transports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (309,'2024_01_24_143100_update_user_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (310,'2024_01_24_144529_drop_events_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (311,'2024_01_24_151845_update_events_contacts_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (312,'2024_01_24_163912_add_uuid_to_orders',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (313,'2024_01_26_183226_add_room_id_to_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (314,'2024_01_30_111120_update_events_texts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (315,'2024_01_30_111500_refactor_order_invoiceable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (316,'2024_01_30_111920_add_mailjet_cols_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (317,'2024_01_30_122803_alter_account_address_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (318,'2024_01_30_155044_main_account_address_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (319,'2024_01_30_162352_alter_order_invoiceable_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (320,'2024_01_30_162953_alter_group_address_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (321,'2024_01_30_170411_add_fo_family_position_to_event_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (322,'2024_01_31_110620_update_account_full_search_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (323,'2024_01_31_111425_update_main_group_address_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (324,'2024_01_31_111446_update_group_export_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (325,'2024_01_31_112005_update_group_fullsearch_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (326,'2024_01_31_112128_update_account_profile_export_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (327,'2024_01_31_112244_update_event_contact_full_search_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (328,'2024_01_31_170057_create_event_contact_sellable_service_choosables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (329,'2024_02_01_141658_create_event_dashboard_intervention_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (330,'2024_02_01_165910_add_is_invitation_to_event_sellable_service',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (331,'2024_02_01_173848_remove_event_contact_sellable_service_choosables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (332,'2024_02_01_174110_create_event_contact_sellable_service_choosables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (333,'2024_02_02_113328_remove_event_sellable_service_choosables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (334,'2024_02_05_145901_create_order_accommodation_attributions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (335,'2024_02_05_174628_update_events_texts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (336,'2024_02_06_153750_update_order_cart',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (337,'2024_02_06_160227_event_contact_dashboard_choosable_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (338,'2024_02_06_164333_remove_profession_id_from_account_profile_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (339,'2024_02_06_165455_add_non_null_profession_id_to_account_profile_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (340,'2024_02_06_173309_recreate_event_accommidation_index_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (341,'2024_02_09_114007_add_origin_to_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (342,'2024_02_12_164023_replace_group_with_sponsor_in_event_program_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (343,'2024_02_12_170306_replace_group_with_sponsor_in_event_program_interventions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (344,'2024_02_12_170807_update_event_program_interventions_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (345,'2024_02_13_101813_update_event_contact_dashboard_intervention_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (346,'2024_02_13_110052_alter_order_payments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (347,'2024_02_13_152756_create_event_contact_dashboard_session_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (348,'2024_02_14_134721_update_event_program_sessions_view',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (349,'2024_02_14_150051_update_events_contacts_view',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (351,'2024_02_14_164620_create_front_carts_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (352,'2024_02_14_172135_create_front_cart_line_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (353,'2024_02_15_100731_add_phone_to_groups_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (354,'2024_02_15_102151_update_group_view',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (355,'2024_02_15_102650_remove_main_contact_id_from_groups_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (356,'2024_02_15_104516_add_main_contact_id_to_event_groups_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (357,'2024_02_15_104517_update_event_groups_view',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (361,'2024_02_15_110059_add_cols_to_event_sellable_service',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (362,'2024_02_15_114437_update_event_contact_sellable_service_choosables_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (363,'2024_02_15_115107_drop_event_sellable_service_choosables',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (364,'2024_02_15_140651_add_vat_id_to_front_cart_lines_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (365,'2024_02_15_172552_create_event_group_contacts_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (366,'2024_02_15_173824_create_event_group_contact_view',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (367,'2024_02_14_114051_alter_order_invoices_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (372,'2024_02_16_090253_update_event_group_contact_view',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (373,'2024_02_16_162521_alter_order_accommodation_cart',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (374,'2024_02_17_090617_update_group_export_view',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (375,'2024_02_17_091227_update_group_fullsearch_view',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (376,'2024_02_17_092309_add_unit_ttc_to_front_cart_lines_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (377,'2024_02_17_101617_add_virtual_stock_to_event_sellable_service_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (378,'2024_02_19_144530_remove_virtual_stock_from_event_sellable_service_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (379,'2024_02_20_151244_remove_uuid_from_front_cart_lines_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (380,'2024_02_20_184329_alter_order_invoices',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (381,'2024_02_22_113035_alter_order_payments_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (382,'2024_02_22_165924_update_events_contacts_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (383,'2024_02_22_174651_update_event_groups_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (384,'2024_02_23_093029_update_event_groups_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (385,'2024_02_23_152953_update_event_program_sessions_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (386,'2024_02_23_161451_add_is_catering_col_to_event_program_sessions_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (387,'2024_02_23_161909_add_is_catering_col_to_event_program_interventions_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (388,'2024_02_23_163657_update_event_program_sessions_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (389,'2024_02_23_164146_update_event_program_interventions_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (390,'2024_02_23_165204_alter_order_invoice_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (391,'2024_02_23_172242_update_event_contact_dashboard_intervention_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (392,'2024_02_23_173000_update_event_contact_dashboard_session_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (393,'2024_02_24_163327_update_event_front_config_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (394,'2024_02_26_152139_update_events_contacts_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (395,'2024_02_27_143124_create_accommodation_accompanying',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (396,'2024_02_27_145254_update_event_program_sessions_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (397,'2024_02_27_150755_update_event_program_interventions_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (398,'2024_02_27_171212_rename_col_in_events_contacts_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (399,'2024_02_27_172721_update_events_contacts_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (400,'2024_02_27_173333_update_event_contact_full_search_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (401,'2024_02_28_110501_event_contact_dashboard_choosable_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (402,'2024_02_28_123616_alter_order_accompanying',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (403,'2024_02_29_185700_add_room_id_to_order_temp_stock',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (404,'2024_03_01_103925_add_col_in_events_contacts_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (405,'2024_03_05_154817_alter_event_accommodation_blocked_room_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (406,'2024_03_06_100147_create_booked_individual_from_blocked',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (407,'2024_03_06_144649_add_col_to_front_carts_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (408,'2024_03_06_153808_add_participation_type_to_temp_order_stock',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (409,'2024_03_06_170411_add_participation_type_to_order_cart_accommodation',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (410,'2024_03_07_153436_create_order_cart_taxroom',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (411,'2024_03_08_152953_add_beneficiary_event_contact_id_col_to_order_cart_service',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (412,'2024_03_11_152135_add_subscribe_cols_to_events_contacts_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (413,'2024_03_11_161212_add_published_col_to_event_accommodation_room',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (414,'2024_03_12_180722_alter_table_order_invoices',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (415,'2024_03_13_161421_add_order_cancellation_col_to_events_contacts_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (416,'2024_03_13_172333_update_event_contact_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (417,'2024_03_14_145421_create_orders_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (418,'2024_03_14_154910_add_cancellation_request_col_to_order_cart_service',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (419,'2024_03_15_104453_update_orders_view',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (420,'2024_03_15_113013_add_cancelled_col_to_order_cart_service',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (421,'2024_03_18_100551_remove_published_col_from_event_accommodation_room',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (422,'2024_03_18_105303_add_meta_info_to_front_cart_lines_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (423,'2024_03_18_172950_create_front_preorders_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (424,'2024_03_19_090901_update_front_preorders_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (425,'2024_03_19_155226_create_front_transactions_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (426,'2024_03_19_165637_update_front_preorders_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (427,'2024_03_20_094449_add_order_ref_col_to_orders_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (428,'2024_03_20_142446_remove_uuid_unique_index_on_front_transactions_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (429,'2024_03_20_143046_remove_order_ref_from_orders_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (430,'2024_03_20_143311_update_orders_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (431,'2024_03_20_145839_add_vat_id_to_event_sellable_service_deposits',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (432,'2024_03_20_160720_add_col_to__events_pec_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (433,'2024_03_22_142410_update_orders_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (434,'2024_03_22_145333_add_cols_to_order_cart_accommodation',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (435,'2024_03_22_171116_add_beneficiary_event_contact_id_to_order_cart_accommodation',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (436,'2024_03_25_092900_remove_cols_from_order_cart_accommodation',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (437,'2024_03_25_154209_update_front_preorders_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (438,'2024_03_25_160157_update_front_transactions_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (439,'2024_03_26_103754_update_order_cart_taxroom',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (440,'2024_03_26_115436_replace_cancelled_with_cancelled_at_col_in_order_cart_service',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (441,'2024_03_26_150639_add_account_info_to_order_temp_stock',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (442,'2024_03_27_135800_add_zone_col_to_event_grant_location',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (443,'2024_03_27_164632_create_continents_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (444,'2024_03_27_171523_add_continent_id_col_to_countries_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (445,'2024_03_28_103827_add_cancellation_request_col_to_order_cart_accommodation',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (446,'2024_03_28_133420_rename_zone_to_continents_in_event_grant_location_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (447,'2024_03_28_170556_create_event_grant_establishments_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (448,'2024_03_28_181736_add_order_counter_to_event_contact_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (449,'2024_03_29_094833_add_continent_col_to_event_grant_allocations_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (450,'2024_03_29_115750_add_orders_count_to_event_group_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (451,'2024_04_02_101222_create_order_cart_grant_waiver_fees',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (452,'2024_04_02_102252_create_order_cart_grant_processing_fees',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (453,'2024_04_02_115029_update_orders_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (454,'2024_04_02_142659_create_event_sellable_service_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (455,'2024_04_02_151252_create_order_cart_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (456,'2024_04_02_170710_add_external_invoice_to_orders',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (457,'2024_04_03_103546_add_external_invoice_status_to_orders_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (458,'2024_04_03_104923_remove_order_cart_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (459,'2024_04_03_105829_add_order_sellable_deposit_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (460,'2024_04_03_115138_relocate_invoice_texts',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (461,'2024_04_03_143453_add_event_id_col_to_order_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (462,'2024_04_03_144650_add_order_sellable_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (463,'2024_04_03_160748_add_pec_enabled_col_to_events_contacts_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (464,'2024_04_03_165638_add_status_col_to_order_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (465,'2024_04_03_171722_update_order_sellable_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (466,'2024_04_03_175210_recreate_order_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (467,'2024_04_03_181546_add_paid_at_to_order_invoices',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (468,'2024_04_03_181805_create_order_invoices_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (469,'2024_04_04_091054_make_vat_id_nullable_in_order_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (470,'2024_04_04_091529_update_order_sellable_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (471,'2024_04_04_103817_update_event_sellable_service_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (472,'2024_04_04_140521_recreate_order_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (473,'2024_04_04_142422_update_order_sellable_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (474,'2024_04_04_145114_add_total_pec_to_front_cart_lines_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (475,'2024_04_04_153009_create_order_room_notes',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (476,'2024_04_04_154447_add_grant_id_to_front_cart_lines_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (477,'2024_04_04_163141_update_front_preorders_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (478,'2024_04_04_175147_add_amount_type_to_event_grant',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (479,'2024_04_05_120126_adjust_orders_invoice_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (480,'2024_04_05_161430_create_order_payments_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (481,'2024_04_05_162820_remove_grant_id_from_front_cart_lines_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (482,'2024_04_08_092657_update_order_sellable_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (483,'2024_04_08_151107_create_order_refunds',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (484,'2024_04_09_102634_recreate_order_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (485,'2024_04_09_144313_update_event_program_sessions_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (486,'2024_04_10_093534_update_event_program_sessions_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (487,'2024_04_10_095554_create_dictionnaries_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (488,'2024_04_10_104445_update_order_sellable_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (489,'2024_04_10_105729_adjust_order_invoices_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (490,'2024_04_10_153157_update_front_transactions_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (491,'2024_04_10_154046_update_front_transactions_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (492,'2024_04_10_154404_update_orders_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (493,'2024_04_10_160315_add_paybox_cols_to_orders_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (494,'2024_04_10_160729_update_orders_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (495,'2024_04_10_162701_add_reimbursed_at_to_order_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (496,'2024_04_10_163336_add_paybox_reimbursement_trans_details_to_order_sellable_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (497,'2024_04_10_171446_rename_order_sellable_deposits_view_to_event_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (498,'2024_04_10_174722_rename_order_sellable_deposits_to_event_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (499,'2024_04_11_090745_update_event_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (500,'2024_04_11_104615_remove_order_cart_grant_waiver_fees',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (501,'2024_04_11_114517_create_paybox_reimbursement_requests',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (502,'2024_04_11_115300_add_paybox_cols_to_event_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (503,'2024_04_11_162133_add_marker_col_to_orders_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (504,'2024_04_11_162944_update_orders_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (505,'2024_04_11_163202_update_event_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (506,'2024_04_11_164335_drop_order_id_fk_in_event_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (507,'2024_04_11_164829_add_order_id_fk_in_event_deposits_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (508,'2024_04_11_165724_update_event_deposits_view',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (509,'2024_04_11_172142_remove_order_cart_grant_processing_fees',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (510,'2024_04_11_172155_add_order_cart_grant_deposit',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (511,'2024_04_12_090039_recreate_order_cart_grant_deposit',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (512,'2024_04_12_093324_recreate_order_cart_sellable_deposit',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (513,'2024_04_12_160255_add_quantity_to_order_tax_room',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (514,'2024_04_12_163554_update_event_group_contact_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (515,'2024_04_15_091729_add_is_pec_eligible_to_events_contacts_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (516,'2024_04_15_095355_update_event_contact_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (517,'2024_04_15_110818_update_orders_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (518,'2024_04_15_155318_add_pec_fees_apply_to_events_contacts_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (519,'2024_04_16_172512_add_last_grant_id_to_events_contacts_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (520,'2024_04_17_091528_update_event_contact_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (521,'2024_04_17_103756_relocate_order_refunds',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (522,'2024_04_17_104055_create_order_refunds_items',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (523,'2024_04_18_115752_create_order_refunds_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (524,'2024_04_18_174413_add_is_placeholder_col_to_event_program_sessions_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (525,'2024_04_19_090933_update_event_program_sessions_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (526,'2024_04_19_091835_add_is_placeholder_col_to_event_program_interventions_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (527,'2024_04_19_091836_update_event_program_interventions_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (528,'2024_04_19_103081_update_front_preorders_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (529,'2024_04_19_112408_create_event_grant_funding_records_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (530,'2024_04_19_153430_update_order_refunds_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (531,'2024_04_19_153431_create_front_my_orders_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (532,'2024_04_23_143952_update_front_transactions_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (533,'2024_04_23_173943_update_event_program_interventions_view',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (534,'2024_04_24_093308_add_amount_used_to_event_grant',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (535,'2024_04_24_095104_create_event_grant_view',22);
