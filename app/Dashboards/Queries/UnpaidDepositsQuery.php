<?php

namespace App\Dashboards\Queries;

use App\Dashboards\Traits\DashboardTrait;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

class UnpaidDepositsQuery
{
    use EventModelTrait;
    use DashboardTrait;

    public function run(): array
    {
        $groups = $this->setGroups()->unsetGroup('industry')->getGroupsAsString();


        $this->queryResponse = DB::select(
            "
WITH deposit_counts AS (
    SELECT
        COALESCE(pt.group, 'unknown') as participation_group,
        COUNT(DISTINCT ec.id) as total
    FROM events_contacts ec
    LEFT JOIN participation_types pt ON ec.participation_type_id = pt.id
    JOIN event_deposits ed ON ed.event_contact_id = ec.id
    WHERE ec.event_id = ?
    AND ed.event_id = ec.event_id
    AND ed.status != 'paid'
    GROUP BY pt.group
)
SELECT
    participation_group,
    total
FROM deposit_counts
WHERE participation_group IN (".$groups.")

UNION ALL

SELECT
    'all' as participation_group,
    COALESCE(SUM(total), 0) as total
FROM deposit_counts
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
