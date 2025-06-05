<?php

namespace App\Dashboards\Queries;

use App\Dashboards\Traits\DashboardTrait;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

class DashboardParticipantsQuery
{
    use EventModelTrait;
    use DashboardTrait;

    public function run(): array
    {
        $groups = $this->setGroups()->getGroupsAsString();

        $this->queryResponse = DB::select(
            "
WITH participant_counts AS (
    SELECT
        COALESCE(pt.group, 'unknown') as participation_group,
        COUNT(DISTINCT ec.id) as total
    FROM events_contacts ec
             LEFT JOIN participation_types pt ON ec.participation_type_id = pt.id
    WHERE ec.event_id = ?
      AND (
        -- Has service orders (not just accommodation)
        EXISTS (
            SELECT 1
            FROM orders o
            WHERE o.client_id = ec.user_id
              AND o.event_id = ec.event_id
              AND o.type = 'order'
              AND (
                EXISTS (SELECT 1 FROM order_cart_service ocs WHERE ocs.order_id = o.id)
                    OR EXISTS (SELECT 1 FROM order_cart_grant_deposit ocgd WHERE ocgd.order_id = o.id)
                    OR EXISTS (SELECT 1 FROM order_cart_sellable_deposit ocsd WHERE ocsd.order_id = o.id)
                )
        )
            OR
            -- Has service attributions (not just accommodation)
        EXISTS (
            SELECT 1
            FROM order_attributions oa
                     JOIN orders o ON oa.order_id = o.id
            WHERE oa.event_contact_id = ec.id
              AND o.event_id = ec.event_id
              AND o.type = 'order'
              AND oa.shoppable_type = 'service'
        )
            OR
            -- Has paid deposit
        EXISTS (
            SELECT 1
            FROM event_deposits ed
            WHERE ed.event_contact_id = ec.id
              AND ed.event_id = ec.event_id
              AND ed.status = 'paid'
        )
        )
    GROUP BY pt.group
)
SELECT
    participation_group,
    total,
    COALESCE(SUM(total) OVER(), 0) as grand_total
FROM participant_counts
WHERE participation_group IN (".$groups.")

UNION ALL

SELECT
    'all' as participation_group,
    COALESCE(SUM(total), 0) as total,
    COALESCE(SUM(total), 0) as grand_total
FROM participant_counts
WHERE participation_group IN (".$groups.")

ORDER BY
    CASE participation_group
        WHEN 'congress' THEN 1
        WHEN 'orator' THEN 2
        WHEN 'industry' THEN 3
        WHEN 'all' THEN 4
        END;
",
            [
                $this->event->id,
            ],
        );

        return [
            'data' => $this->queryResponse,
            'groups' => $this->getGroups(),
        ];
    }
}
