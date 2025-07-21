<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `event_deposits` CHANGE COLUMN `beneficiary_event_contact_id` `event_contact_id` BIGINT(20) UNSIGNED NOT NULL AFTER `vat_id`");

        DB::statement(
            "CREATE OR REPLACE VIEW `event_deposits_view` AS
select `ed`.`id`                                                                          AS `id`,
       `ed`.`order_id`                                                                    AS `order_id`,
       `o`.`uuid`                                                                         AS `uuid`,
       `ed`.`event_id`                                                                    AS `event_id`,
       `ed`.`shoppable_type`                                                              AS `shoppable_type`,
       `ed`.`shoppable_label`                                                             AS `shoppable_label`,
       `ed`.`total_net`                                                                   AS `total_net`,
       `ed`.`event_contact_id`                                                            AS `event_contact_id`,
       `ed`.`status`                                                                      AS `status`,
       `ed`.`reimbursed_at`                                                               AS `reimbursed_at`,
       case when `oi`.`order_id` is not null then 1 else NULL end                         AS `has_invoice`,
       case when `e`.`ends` < curdate() and `ec`.`is_attending` is null then 1 else 0 end AS `is_attending_expired`,
       `ed`.`total_net` + `ed`.`total_vat`                                                AS `total_ttc`,
       date_format(`ed`.`created_at`, '%d/%m/%Y %H:%i:%s')                                AS `date_fr`,
       concat(`u`.`first_name`, ' ', `u`.`last_name`)                                     AS `beneficiary_name`,
       `pc`.`id`                                                                          AS `payment_call_id`
from ((((((`event_deposits` `ed` left join `orders` `o` on (`ed`.`order_id` = `o`.`id`)) left join `order_invoices` `oi`
          on (`oi`.`order_id` = `o`.`id`)) left join `events_contacts` `ec`
         on (`ed`.`event_contact_id` = `ec`.`id`)) left join `users` `u`
        on (`ec`.`user_id` = `u`.`id`)) left join `events` `e`
       on (`ed`.`event_id` = `e`.`id`)) left join `payment_call` `pc`
      on (`pc`.`shoppable_type` = 'App\\Models\\Order\\EventDeposit' and `pc`.`shoppable_id` = `ed`.`id`))
where `ed`.`status` <> 'temp'",
        );


        DB::statement("ALTER TABLE `order_cart_accommodation`	DROP FOREIGN KEY `order_cart_accommodation_beneficiary_event_contact_id_foreign`");
        DB::statement(
            "ALTER TABLE `order_cart_accommodation`
	CHANGE COLUMN `beneficiary_event_contact_id` `event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `comment`,
	DROP INDEX `order_cart_accommodation_beneficiary_event_contact_id_foreign`,
	ADD INDEX `order_cart_accommodation_beneficiary_event_contact_id_foreign` (`event_contact_id`) USING BTREE,
	ADD CONSTRAINT `order_cart_accommodation_beneficiary_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT",
        );

        DB::statement("ALTER TABLE `order_cart_grant_deposit`	DROP FOREIGN KEY `ocgpf_beneficiary_contact_fk`");
        DB::statement(
            "ALTER TABLE `order_cart_grant_deposit`
	CHANGE COLUMN `beneficiary_event_contact_id` `event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `total_vat`,
	DROP INDEX `ocgpf_beneficiary_contact_fk`,
	ADD INDEX `ocgpf_beneficiary_contact_fk` (`event_contact_id`) USING BTREE,
	ADD CONSTRAINT `ocgpf_beneficiary_contact_fk` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE CASCADE",
        );

        DB::statement("ALTER TABLE `order_cart_sellable_deposit`	DROP FOREIGN KEY `ocsd_beneficiary_contact_fk`");
        DB::statement("ALTER TABLE `order_cart_sellable_deposit`
	CHANGE COLUMN `beneficiary_event_contact_id` `event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `total_vat`,
	DROP INDEX `ocsd_beneficiary_contact_fk`,
	ADD INDEX `ocsd_beneficiary_contact_fk` (`event_contact_id`) USING BTREE,
	ADD CONSTRAINT `ocsd_beneficiary_contact_fk` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE CASCADE");


        DB::statement("ALTER TABLE `order_cart_service` DROP FOREIGN KEY `order_cart_service_beneficiary_event_contact_id_foreign`");
        DB::statement("ALTER TABLE `order_cart_service`
        CHANGE COLUMN `beneficiary_event_contact_id` `event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `updated_at`,
        DROP INDEX `order_cart_service_beneficiary_event_contact_id_foreign`,
        ADD INDEX `order_cart_service_beneficiary_event_contact_id_foreign` (`event_contact_id`) USING BTREE,
        ADD CONSTRAINT `order_cart_service_beneficiary_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT");

        DB::statement("ALTER TABLE `order_cart_taxroom` DROP FOREIGN KEY `order_cart_taxroom_beneficiary_event_contact_id_foreign`");
        DB::statement("ALTER TABLE `order_cart_taxroom`
	CHANGE COLUMN `beneficiary_event_contact_id` `event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `updated_at`,
	DROP INDEX `order_cart_taxroom_beneficiary_event_contact_id_foreign`,
	ADD INDEX `order_cart_taxroom_beneficiary_event_contact_id_foreign` (`event_contact_id`) USING BTREE,
	ADD CONSTRAINT `order_cart_taxroom_beneficiary_event_contact_id_foreign` FOREIGN KEY (`event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT");



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `event_deposits` CHANGE COLUMN `event_contact_id` `beneficiary_event_contact_id`  BIGINT(20) UNSIGNED NOT NULL AFTER `vat_id`");

        DB::statement(
            "CREATE OR REPLACE VIEW `event_deposits_view` AS
select `ed`.`id`                                                                          AS `id`,
       `ed`.`order_id`                                                                    AS `order_id`,
       `o`.`uuid`                                                                         AS `uuid`,
       `ed`.`event_id`                                                                    AS `event_id`,
       `ed`.`shoppable_type`                                                              AS `shoppable_type`,
       `ed`.`shoppable_label`                                                             AS `shoppable_label`,
       `ed`.`total_net`                                                                   AS `total_net`,
       `ed`.`beneficiary_event_contact_id`                                                AS `beneficiary_event_contact_id`,
       `ed`.`status`                                                                      AS `status`,
       `ed`.`reimbursed_at`                                                               AS `reimbursed_at`,
       case when `oi`.`order_id` is not null then 1 else NULL end                         AS `has_invoice`,
       case when `e`.`ends` < curdate() and `ec`.`is_attending` is null then 1 else 0 end AS `is_attending_expired`,
       `ed`.`total_net` + `ed`.`total_vat`                                                AS `total_ttc`,
       date_format(`ed`.`created_at`, '%d/%m/%Y %H:%i:%s')                                AS `date_fr`,
       concat(`u`.`first_name`, ' ', `u`.`last_name`)                                     AS `beneficiary_name`,
       `pc`.`id`                                                                          AS `payment_call_id`
from ((((((`event_deposits` `ed` left join `orders` `o` on (`ed`.`order_id` = `o`.`id`)) left join `order_invoices` `oi`
          on (`oi`.`order_id` = `o`.`id`)) left join `events_contacts` `ec`
         on (`ed`.`beneficiary_event_contact_id` = `ec`.`id`)) left join `users` `u`
        on (`ec`.`user_id` = `u`.`id`)) left join `events` `e`
       on (`ed`.`event_id` = `e`.`id`)) left join `payment_call` `pc`
      on (`pc`.`shoppable_type` = 'App\\Models\\Order\\EventDeposit' and `pc`.`shoppable_id` = `ed`.`id`))
where `ed`.`status` <> 'temp'",
        );

        // Reverse for order_cart_accommodation
        DB::statement("ALTER TABLE `order_cart_accommodation` DROP FOREIGN KEY `order_cart_accommodation_beneficiary_event_contact_id_foreign`");
        DB::statement(
            "ALTER TABLE `order_cart_accommodation`
    CHANGE COLUMN `event_contact_id` `beneficiary_event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `comment`,
    DROP INDEX `order_cart_accommodation_beneficiary_event_contact_id_foreign`,
    ADD INDEX `order_cart_accommodation_beneficiary_event_contact_id_foreign` (`beneficiary_event_contact_id`) USING BTREE,
    ADD CONSTRAINT `order_cart_accommodation_beneficiary_event_contact_id_foreign` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT"
        );

// Reverse for order_cart_grant_deposit
        DB::statement("ALTER TABLE `order_cart_grant_deposit` DROP FOREIGN KEY `ocgpf_beneficiary_contact_fk`");
        DB::statement(
            "ALTER TABLE `order_cart_grant_deposit`
    CHANGE COLUMN `event_contact_id` `beneficiary_event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `total_vat`,
    DROP INDEX `ocgpf_beneficiary_contact_fk`,
    ADD INDEX `ocgpf_beneficiary_contact_fk` (`beneficiary_event_contact_id`) USING BTREE,
    ADD CONSTRAINT `ocgpf_beneficiary_contact_fk` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE CASCADE"
        );

// Reverse for order_cart_sellable_deposit
        DB::statement("ALTER TABLE `order_cart_sellable_deposit` DROP FOREIGN KEY `ocsd_beneficiary_contact_fk`");
        DB::statement(
            "ALTER TABLE `order_cart_sellable_deposit`
    CHANGE COLUMN `event_contact_id` `beneficiary_event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `total_vat`,
    DROP INDEX `ocsd_beneficiary_contact_fk`,
    ADD INDEX `ocsd_beneficiary_contact_fk` (`beneficiary_event_contact_id`) USING BTREE,
    ADD CONSTRAINT `ocsd_beneficiary_contact_fk` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE CASCADE"
        );

// Reverse for order_cart_service
        DB::statement("ALTER TABLE `order_cart_service` DROP FOREIGN KEY `order_cart_service_beneficiary_event_contact_id_foreign`");
        DB::statement(
            "ALTER TABLE `order_cart_service`
    CHANGE COLUMN `event_contact_id` `beneficiary_event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `updated_at`,
    DROP INDEX `order_cart_service_beneficiary_event_contact_id_foreign`,
    ADD INDEX `order_cart_service_beneficiary_event_contact_id_foreign` (`beneficiary_event_contact_id`) USING BTREE,
    ADD CONSTRAINT `order_cart_service_beneficiary_event_contact_id_foreign` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT"
        );

// Reverse for order_cart_taxroom
        DB::statement("ALTER TABLE `order_cart_taxroom` DROP FOREIGN KEY `order_cart_taxroom_beneficiary_event_contact_id_foreign`");
        DB::statement(
            "ALTER TABLE `order_cart_taxroom`
    CHANGE COLUMN `event_contact_id` `beneficiary_event_contact_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `updated_at`,
    DROP INDEX `order_cart_taxroom_beneficiary_event_contact_id_foreign`,
    ADD INDEX `order_cart_taxroom_beneficiary_event_contact_id_foreign` (`beneficiary_event_contact_id`) USING BTREE,
    ADD CONSTRAINT `order_cart_taxroom_beneficiary_event_contact_id_foreign` FOREIGN KEY (`beneficiary_event_contact_id`) REFERENCES `events_contacts` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT"
        );
    }
};
