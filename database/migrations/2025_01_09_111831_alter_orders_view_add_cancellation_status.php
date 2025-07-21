<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "CREATE OR REPLACE VIEW orders_view AS
        SELECT o.id                                                   AS id,
       o.uuid                                                 AS uuid,
       o.event_id                                             AS event_id,
       o.created_at                                           AS date,
       o.client_type                                          AS client_type,
       o.origin                                               AS origin,
       o.marker                                               AS marker,
       o.cancellation_status                                  AS cancellation_status,
       CASE
           WHEN 'group' COLLATE utf8mb4_unicode_ci = o.client_type THEN 'Groupe'
           WHEN 'contact' COLLATE utf8mb4_unicode_ci = o.client_type THEN 'Participant'
           WHEN 'orator' COLLATE utf8mb4_unicode_ci = o.client_type THEN 'Orateur'
           END                                                AS client_type_display,
       o.client_id                                            AS client_id,
       o.status                                               AS status,
       ft.transaction_id                                      AS paybox_num_trans,
       CASE
           WHEN 'orator' COLLATE utf8mb4_unicode_ci = o.client_type THEN '-'
           WHEN 'paid' COLLATE utf8mb4_unicode_ci = o.status THEN 'Soldée'
           WHEN 'unpaid' COLLATE utf8mb4_unicode_ci = o.status THEN 'Non-soldée'
           END                                                AS status_display,
       oi.invoice_number                                      AS invoice_number,
       CASE
           WHEN 'group' COLLATE utf8mb4_unicode_ci = o.client_type THEN g.name
           WHEN o.client_type IN ('contact' COLLATE utf8mb4_unicode_ci,
                                  'orator' COLLATE utf8mb4_unicode_ci)
               THEN CONCAT(u.last_name, ' ', u.first_name)
           END                                                AS name,
       FORMAT((o.total_net + o.total_vat) / 100, 2)           AS total,
       FORMAT(COALESCE(p.payments_total, 0) / 100, 2)         AS payments_total,
       FORMAT(o.total_pec / 100, 2)                           AS total_pec,
       ec.order_cancellation                                  AS order_cancellation,
       CASE
           WHEN o.cancellation_request IS NOT NULL THEN o.cancellation_request
           WHEN ocs.cancellation_request IS NOT NULL THEN ocs.cancellation_request
           WHEN oca.cancellation_request IS NOT NULL THEN oca.cancellation_request
           ELSE NULL
           END                                                AS cancellation_request,
       CASE
           WHEN o.cancelled_at IS NOT NULL THEN o.cancelled_at
           WHEN ocs.cancelled_at IS NOT NULL THEN ocs.cancelled_at
           WHEN oca.cancelled_at IS NOT NULL THEN oca.cancelled_at
           ELSE NULL
           END                                                AS cancelled_at,
       CASE WHEN oi.order_id IS NOT NULL THEN 1 ELSE NULL END AS has_invoice,
       CASE
           WHEN 'orator' COLLATE utf8mb4_unicode_ci = o.client_type THEN '-'
           WHEN oi.order_id IS NOT NULL THEN 'Oui'
           ELSE 'Non'
           END                                                AS has_invoice_display,
       CASE
           WHEN EXISTS(SELECT 1 FROM order_cart_service ocs1 WHERE ocs1.order_id = o.id LIMIT 1)
               AND EXISTS(SELECT 1 FROM order_cart_accommodation oca1 WHERE oca1.order_id = o.id LIMIT 1)
               THEN 'Prestations, Hébergement'
           WHEN EXISTS(SELECT 1 FROM order_cart_service ocs1 WHERE ocs1.order_id = o.id LIMIT 1)
               THEN 'Prestations'
           WHEN EXISTS(SELECT 1 FROM order_cart_accommodation oca1 WHERE oca1.order_id = o.id LIMIT 1)
               THEN 'Hébergement'
           ELSE '-'
           END                                                AS contains,
       CASE WHEN o.total_pec > 0 THEN 'PEC' ELSE NULL END     AS has_pec,
       CASE
           WHEN o.amended_by_order_id IS NOT NULL
               THEN CONCAT('modifiée par #', o.amended_by_order_id)
           ELSE ''
           END                                                AS amended_by_order,
       CASE
           WHEN ec.order_cancellation IS NOT NULL
               THEN 'ne vient plus'
           WHEN 'full' COLLATE utf8mb4_unicode_ci = o.cancellation_status THEN 'complète'
           WHEN 'partial' COLLATE utf8mb4_unicode_ci = o.cancellation_status THEN 'partielle'
           ELSE ''
           END                                                AS cancellation_status_display,
       CONCAT(
           DATE_FORMAT(o.created_at, '%d '),
           CASE
               WHEN '01' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Janvier'
               WHEN '02' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Février'
               WHEN '03' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Mars'
               WHEN '04' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Avril'
               WHEN '05' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Mai'
               WHEN '06' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Juin'
               WHEN '07' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Juillet'
               WHEN '08' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Août'
               WHEN '09' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Septembre'
               WHEN '10' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Octobre'
               WHEN '11' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Novembre'
               WHEN '12' COLLATE utf8mb4_unicode_ci = DATE_FORMAT(o.created_at, '%m') THEN 'Décembre'
               END,
           DATE_FORMAT(o.created_at, ' %Y à %H:%i')
       )                                                      AS date_display
FROM orders o
         LEFT JOIN order_invoices oi ON oi.order_id = o.id
         LEFT JOIN front_transactions ft ON ft.order_id = o.id
         LEFT JOIN groups g ON ('group' COLLATE utf8mb4_unicode_ci = o.client_type AND g.id = o.client_id)
         LEFT JOIN users u ON (
    o.client_type IN (
                      'contact' COLLATE utf8mb4_unicode_ci,
                      'orator' COLLATE utf8mb4_unicode_ci
        )
        AND u.id = o.client_id
    )
         LEFT JOIN (SELECT op.order_id AS order_id, SUM(op.amount) AS payments_total
                    FROM order_payments op
                    GROUP BY op.order_id) p ON p.order_id = o.id
         LEFT JOIN events_contacts ec ON (
    o.client_type IN (
                      'contact' COLLATE utf8mb4_unicode_ci,
                      'orator' COLLATE utf8mb4_unicode_ci
        )
        AND u.id = ec.user_id
        AND o.event_id = ec.event_id
    )
         LEFT JOIN (SELECT ocs.order_id                  AS order_id,
                           MAX(ocs.cancellation_request) AS cancellation_request,
                           MAX(ocs.cancelled_at)         AS cancelled_at
                    FROM order_cart_service ocs
                    GROUP BY ocs.order_id) ocs ON ocs.order_id = o.id
         LEFT JOIN (SELECT oca.order_id                  AS order_id,
                           MAX(oca.cancellation_request) AS cancellation_request,
                           MAX(oca.cancelled_at)         AS cancelled_at
                    FROM order_cart_accommodation oca
                    GROUP BY oca.order_id) oca ON oca.order_id = o.id
WHERE o.parent_id IS NULL
GROUP BY o.id
        ",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
