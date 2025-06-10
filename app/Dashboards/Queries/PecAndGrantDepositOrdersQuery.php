<?php

namespace App\Dashboards\Queries;

use App\Dashboards\Traits\DashboardTrait;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

class PecAndGrantDepositOrdersQuery
{
    use EventModelTrait;
    use DashboardTrait;

    public function run(): array
    {
        $groups = $this->setGroups()->unsetGroup('industry')->getGroupsAsString();

        $this->queryResponse = DB::select(
            "
WITH order_counts AS (
    SELECT
        COALESCE(pt.group, 'unknown') as participation_group,
        COUNT(DISTINCT ec.id) as total
    FROM events_contacts ec
    LEFT JOIN participation_types pt ON ec.participation_type_id = pt.id
    JOIN orders o ON o.client_id = ec.user_id AND o.event_id = ec.event_id
    WHERE ec.event_id = ?
    AND (
        -- Orders with PEC
        (o.type = 'order' AND o.total_pec > 0)
        OR
        -- Paid grant deposits
        (o.type = 'grantdeposit' AND o.status = 'paid')
    )
    GROUP BY pt.group
)
SELECT
    participation_group,
    total
FROM order_counts
WHERE participation_group IN (".$groups.")

UNION ALL

SELECT
    'all' as participation_group,
    COALESCE(SUM(total), 0) as total
FROM order_counts
WHERE participation_group IN (".$groups.")

ORDER BY
    CASE participation_group
        WHEN 'congress' THEN 1
        WHEN 'orator' THEN 2
        WHEN 'all' THEN 3
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
