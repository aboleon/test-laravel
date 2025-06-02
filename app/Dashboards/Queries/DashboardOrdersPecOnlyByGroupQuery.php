<?php

namespace App\Dashboards\Queries;

use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

/**
 * Toutes les commandes de type 'orator'
 */
class DashboardOrdersPecOnlyByGroupQuery
{
    use EventModelTrait;

    public function run()
    {

        return DB::select(
            "
        -- First, create a subquery to get all the detailed data by participation group
WITH group_totals AS (
    SELECT
        -- Create a proper grouping field that categorizes NULL participation types accurately
        CASE
            WHEN o.client_type = 'group' OR ec.id IS NULL THEN 'no_contact'
            WHEN pt.id IS NULL OR pt.group IS NULL THEN 'undefined'
            ELSE pt.group
        END AS participation_group,

        SUM(CASE WHEN ocs.id IS NOT NULL THEN ocs.total_net ELSE 0 END) AS services_total_net,
        SUM(CASE WHEN ocs.id IS NOT NULL THEN ocs.total_vat ELSE 0 END) AS services_total_vat,
        SUM(CASE WHEN ocs.id IS NOT NULL THEN (ocs.total_net + ocs.total_vat + ocs.total_pec) ELSE 0 END) AS services_total,

        SUM(CASE WHEN oca.id IS NOT NULL THEN oca.total_net ELSE 0 END) AS accom_total_net,
        SUM(CASE WHEN oca.id IS NOT NULL THEN oca.total_vat ELSE 0 END) AS accom_total_vat,
        SUM(CASE WHEN oca.id IS NOT NULL THEN (oca.total_net + oca.total_vat + oca.total_pec) ELSE 0 END) AS accom_total,

        SUM(CASE WHEN oct.id IS NOT NULL THEN oct.amount_net ELSE 0 END) AS taxroom_total_net,
        SUM(CASE WHEN oct.id IS NOT NULL THEN oct.amount_vat ELSE 0 END) AS taxroom_total_vat,
        SUM(CASE WHEN oct.id IS NOT NULL THEN (oct.amount_net + oct.amount_vat + oct.amount_pec) ELSE 0 END) AS taxroom_total
    FROM
        orders o
    LEFT JOIN
        events_contacts ec ON o.client_id = ec.id AND o.client_type != 'group'
    LEFT JOIN
        participation_types pt ON ec.participation_type_id = pt.id
    LEFT JOIN
        order_cart_service ocs ON o.id = ocs.order_id AND ocs.total_pec != 0 AND ocs.cancelled_at IS NULL
    LEFT JOIN
        order_cart_accommodation oca ON o.id = oca.order_id AND oca.total_pec != 0 AND oca.cancelled_at IS NULL
    LEFT JOIN
        order_cart_taxroom oct ON o.id = oct.order_id AND oct.amount_pec != 0
    WHERE
        o.event_id = :event_id
    GROUP BY
        participation_group
)

-- First, the categorized groups
SELECT
    participation_group,
    FORMAT(services_total_net / 100, 2) AS services_total_net,
    FORMAT(services_total_vat / 100, 2) AS services_total_vat,
    FORMAT(services_total / 100, 2) AS services_total,
    FORMAT((accom_total_net + taxroom_total_net) / 100, 2) AS accommodations_total_net,
    FORMAT((accom_total_vat + taxroom_total_vat) / 100, 2) AS accommodations_total_vat,
    FORMAT((accom_total + taxroom_total) / 100, 2) AS accommodations_total,
    FORMAT((services_total_net + accom_total_net + taxroom_total_net) / 100, 2) AS total_net,
    FORMAT((services_total_vat + accom_total_vat + taxroom_total_vat) / 100, 2) AS total_vat,
    FORMAT((services_total + accom_total + taxroom_total) / 100, 2) AS total
FROM
    group_totals
WHERE
    participation_group IN ('congress', 'industry', 'orator')

UNION ALL

SELECT
    'all' AS participation_group,
    FORMAT(SUM(services_total_net) / 100, 2) AS services_total_net,
    FORMAT(SUM(services_total_vat) / 100, 2) AS services_total_vat,
    FORMAT(SUM(services_total) / 100, 2) AS services_total,
    FORMAT(SUM(accom_total_net + taxroom_total_net) / 100, 2) AS accommodations_total_net,
    FORMAT(SUM(accom_total_vat + taxroom_total_vat) / 100, 2) AS accommodations_total_vat,
    FORMAT(SUM(accom_total + taxroom_total) / 100, 2) AS accommodations_total,
    FORMAT(SUM(services_total_net + accom_total_net + taxroom_total_net) / 100, 2) AS total_net,
    FORMAT(SUM(services_total_vat + accom_total_vat + taxroom_total_vat) / 100, 2) AS total_vat,
    FORMAT(SUM(services_total + accom_total + taxroom_total) / 100, 2) AS total
FROM
    group_totals",
            [
                'event_id' => $this->event->id,
            ],
        );
    }
}
