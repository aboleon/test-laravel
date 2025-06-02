<?php

namespace App\Dashboards\Queries;

use App\Enum\OrderClientType;
use App\Enum\OrderMarker;
use App\Enum\OrderType;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

/**
 * Toutes les commandes 'client' & 'group' hors 'orator', avec différenciation par les trois grands groupes et hébergement à part
 * 1. Toutes commandes PEC ou non Tout type, soldées ou non
 * 2. Commandes soldées PEC ou non Tout type
 * 3. Commandes non soldées PEC ou non Tout type
 * 4. Commandes soldées PEC ou non HEBERGEMENT
 * 5. Commandes non soldées PEC ou non HEBERGEMENT
 */
class DashboardOneQuery
{
    use EventModelTrait;

    public function run(): array
    {
        return DB::select(
            "
-- First query: Non-group orders
SELECT
    'non_group' AS order_type,
    -- Total orders - all groups
    CAST(SUM(
        IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
        IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
        IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
    ) AS SIGNED) AS total_orders,

    -- Paid orders - all groups
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders,

    -- Unpaid orders - all groups
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders,

    -- Total accommodation - all groups
    CAST(SUM(
        IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
        IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
    ) AS SIGNED) AS total_orders_accommodation,

    -- Paid accommodation - all groups
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_accommodation,

    -- Unpaid accommodation - all groups
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_accommodation,

    -- CONGRESS GROUP
    -- Total orders - congress
    CAST(SUM(
        CASE WHEN pt.group = 'congress' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS total_orders_congress,

    -- Paid orders - congress
    CAST(SUM(
        CASE WHEN pt.group = 'congress' AND o.status = 'paid' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_congress,

    -- Unpaid orders - congress
    CAST(SUM(
        CASE WHEN pt.group = 'congress' AND o.status != 'paid' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_congress,

    -- Paid accommodation - congress
    CAST(SUM(
        CASE WHEN pt.group = 'congress' AND o.status = 'paid' THEN
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_accommodation_congress,

    -- Unpaid accommodation - congress
    CAST(SUM(
        CASE WHEN pt.group = 'congress' AND o.status != 'paid' THEN
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_accommodation_congress,

    -- INDUSTRY GROUP
    -- Total orders - industry
    CAST(SUM(
        CASE WHEN pt.group = 'industry' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS total_orders_industry,

    -- Paid orders - industry
    CAST(SUM(
        CASE WHEN pt.group = 'industry' AND o.status = 'paid' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_industry,

    -- Unpaid orders - industry
    CAST(SUM(
        CASE WHEN pt.group = 'industry' AND o.status != 'paid' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_industry,

    -- Paid accommodation - industry
    CAST(SUM(
        CASE WHEN pt.group = 'industry' AND o.status = 'paid' THEN
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_accommodation_industry,

    -- Unpaid accommodation - industry
    CAST(SUM(
        CASE WHEN pt.group = 'industry' AND o.status != 'paid' THEN
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_accommodation_industry,

    -- ORATOR GROUP
    -- Total orders - orator
    CAST(SUM(
        CASE WHEN pt.group = 'orator' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS total_orders_orator,

    -- Paid orders - orator
    CAST(SUM(
        CASE WHEN pt.group = 'orator' AND o.status = 'paid' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_orator,

    -- Unpaid orders - orator
    CAST(SUM(
        CASE WHEN pt.group = 'orator' AND o.status != 'paid' THEN
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_orator,

    -- Paid accommodation - orator
    CAST(SUM(
        CASE WHEN pt.group = 'orator' AND o.status = 'paid' THEN
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_accommodation_orator,

    -- Unpaid accommodation - orator
    CAST(SUM(
        CASE WHEN pt.group = 'orator' AND o.status != 'paid' THEN
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_accommodation_orator,

    NULL AS unassigned_amount
FROM orders o
LEFT JOIN events_contacts ec ON o.client_id = ec.user_id AND o.client_type != 'group' AND ec.event_id = o.event_id
LEFT JOIN participation_types pt ON ec.participation_type_id = pt.id
WHERE o.event_id = ? AND o.type='".OrderType::ORDER->value."' AND o.marker='".OrderMarker::NORMAL->value."' AND  client_type = '".OrderClientType::CONTACT->value."'
AND o.cancelled_at IS NULL
GROUP BY o.event_id

UNION ALL

-- Second query: Group orders with attribution
SELECT
    'group' AS order_type,
    -- Total orders - all groups with attribution (FIXED with direct sum)
    CAST(SUM(
        IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs WHERE ocs.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
        IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca WHERE oca.order_id = o.id AND oca.cancelled_at IS NULL), 0) +
        IFNULL((SELECT SUM(oct.amount_net + oct.amount_pec) FROM order_cart_taxroom oct WHERE oct.order_id = o.id), 0)
    ) AS SIGNED) AS total_orders,

    -- Paid orders - all groups with attribution
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity)) FROM order_attributions oa JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service' WHERE oa.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity)) FROM order_attributions oa JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation' WHERE oa.order_id = o.id AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders,

    -- Unpaid orders - all groups with attribution
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity)) FROM order_attributions oa JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service' WHERE oa.order_id = o.id AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity)) FROM order_attributions oa JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation' WHERE oa.order_id = o.id AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders,

    -- Total accommodation - all groups with attribution
    CAST(SUM(
        IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity)) FROM order_attributions oa JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation' WHERE oa.order_id = o.id AND oca.cancelled_at IS NULL), 0)
    ) AS SIGNED) AS total_orders_accommodation,

    -- Paid accommodation - all groups with attribution
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity)) FROM order_attributions oa JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation' WHERE oa.order_id = o.id AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_accommodation,

    -- Unpaid accommodation - all groups with attribution
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity)) FROM order_attributions oa JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation' WHERE oa.order_id = o.id AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_accommodation,

    -- CONGRESS GROUP
    -- Total orders - congress
    CAST(SUM(
        IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                FROM order_attributions oa
                JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                JOIN participation_types pt ON ec.participation_type_id = pt.id
                WHERE oa.order_id = o.id AND pt.group = 'congress' AND ocs.cancelled_at IS NULL), 0) +
        IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                FROM order_attributions oa
                JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                JOIN participation_types pt ON ec.participation_type_id = pt.id
                WHERE oa.order_id = o.id AND pt.group = 'congress' AND oca.cancelled_at IS NULL), 0)
    ) AS SIGNED) AS total_orders_congress,

    -- Paid orders - congress
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'congress' AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'congress' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_congress,

    -- Unpaid orders - congress
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'congress' AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'congress' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_congress,

    -- Paid accommodation - congress
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'congress' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_accommodation_congress,

    -- Unpaid accommodation - congress
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'congress' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_accommodation_congress,

    -- INDUSTRY GROUP
    -- Total orders - industry
    CAST(SUM(
        IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                FROM order_attributions oa
                JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                JOIN participation_types pt ON ec.participation_type_id = pt.id
                WHERE oa.order_id = o.id AND pt.group = 'industry' AND ocs.cancelled_at IS NULL), 0) +
        IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                FROM order_attributions oa
                JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                JOIN participation_types pt ON ec.participation_type_id = pt.id
                WHERE oa.order_id = o.id AND pt.group = 'industry' AND oca.cancelled_at IS NULL), 0)
    ) AS SIGNED) AS total_orders_industry,

    -- Paid orders - industry
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'industry' AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'industry' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_industry,

    -- Unpaid orders - industry
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'industry' AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'industry' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_industry,

    -- Paid accommodation - industry
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'industry' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_accommodation_industry,

    -- Unpaid accommodation - industry
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'industry' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_accommodation_industry,

    -- ORATOR GROUP
    -- Total orders - orator
    CAST(SUM(
        IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                FROM order_attributions oa
                JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                JOIN participation_types pt ON ec.participation_type_id = pt.id
                WHERE oa.order_id = o.id AND pt.group = 'orator' AND ocs.cancelled_at IS NULL), 0) +
        IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                FROM order_attributions oa
                JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                JOIN participation_types pt ON ec.participation_type_id = pt.id
                WHERE oa.order_id = o.id AND pt.group = 'orator' AND oca.cancelled_at IS NULL), 0)
    ) AS SIGNED) AS total_orders_orator,

    -- Paid orders - orator
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'orator' AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'orator' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_orator,

    -- Unpaid orders - orator
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM((ocs.total_net / oa.quantity) + (ocs.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'orator' AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'orator' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_orator,

    -- Paid accommodation - orator
    CAST(SUM(
        CASE WHEN o.status = 'paid' THEN
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'orator' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS paid_orders_accommodation_orator,

    -- Unpaid accommodation - orator
    CAST(SUM(
        CASE WHEN o.status != 'paid' THEN
            IFNULL((SELECT SUM((oca.total_net / oa.quantity) + (oca.total_pec / oa.quantity))
                   FROM order_attributions oa
                   JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                   JOIN events_contacts ec ON oa.event_contact_id = ec.id AND ec.event_id = o.event_id
                   JOIN participation_types pt ON ec.participation_type_id = pt.id
                   WHERE oa.order_id = o.id AND pt.group = 'orator' AND oca.cancelled_at IS NULL), 0)
        ELSE 0 END
    ) AS SIGNED) AS unpaid_orders_accommodation_orator,

    -- Unassigned amount using CAST to integer
    CAST(
        (
            -- Total services and accommodations
            IFNULL((SELECT SUM(ocs.total_net + ocs.total_pec) FROM order_cart_service ocs
                    WHERE ocs.order_id IN (SELECT id FROM orders WHERE event_id = ? AND client_type = 'group' AND cancelled_at IS NULL)
                    AND ocs.cancelled_at IS NULL), 0) +
            IFNULL((SELECT SUM(oca.total_net + oca.total_pec) FROM order_cart_accommodation oca
                    WHERE oca.order_id IN (SELECT id FROM orders WHERE event_id = ? AND client_type = 'group' AND cancelled_at IS NULL)
                    AND oca.cancelled_at IS NULL), 0) -
            -- Attributed amounts with integer division and multiplication
            COALESCE((SELECT SUM(
                        (oa.quantity * ocs.total_net) DIV ocs.quantity +
                        (oa.quantity * ocs.total_pec) DIV ocs.quantity
                    )
                    FROM order_attributions oa
                    JOIN order_cart_service ocs ON oa.shoppable_id = ocs.id AND oa.shoppable_type = 'service'
                    WHERE oa.order_id IN (SELECT id FROM orders WHERE event_id = ? AND client_type = 'group' AND cancelled_at IS NULL)
                    AND ocs.cancelled_at IS NULL
                    AND ocs.quantity > 0), 0) -
            COALESCE((SELECT SUM(
                        (oa.quantity * oca.total_net) DIV oca.quantity +
                        (oa.quantity * oca.total_pec) DIV oca.quantity
                    )
                    FROM order_attributions oa
                    JOIN order_cart_accommodation oca ON oa.shoppable_id = oca.id AND oa.shoppable_type = 'accommodation'
                    WHERE oa.order_id IN (SELECT id FROM orders WHERE event_id = ? AND client_type = 'group' AND cancelled_at IS NULL)
                    AND oca.cancelled_at IS NULL
                    AND oca.quantity > 0), 0)
        ) AS SIGNED
    ) AS unassigned_amount
FROM orders o
WHERE o.event_id = ? AND o.type='".OrderType::ORDER->value."' AND o.marker='".OrderMarker::NORMAL->value."' AND client_type = '".OrderClientType::GROUP->value."'
AND o.cancelled_at IS NULL
GROUP BY o.event_id
",
            [
                $this->event->id, // First parameter for the first WHERE clause
                $this->event->id, // Second parameter for the unassigned amount calculation
                $this->event->id, // Third parameter for the unassigned amount calculation
                $this->event->id, // Fourth parameter for the unassigned amount calculation
                $this->event->id, // Fifth parameter for the unassigned amount calculation
                $this->event->id, // Sixth parameter for the second WHERE clause
            ],
        );
    }
}
