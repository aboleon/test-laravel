<?php

namespace App\Dashboards\Queries;

use App\Dashboards\Traits\DashboardTrait;
use App\Enum\OrderCartType;
use App\Enum\OrderClientType;
use App\Enum\OrderType;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

class EventContactCountByServiceFamilyQuery
{
    use EventModelTrait;
    use DashboardTrait;

    public function run(): array
    {

        $this->queryResponse = DB::select(
            "
WITH service_contacts AS (
   -- Get contacts who bought services directly
   SELECT DISTINCT
       ec.id as contact_id,
       ess.service_group as family_id
   FROM events_contacts ec
   JOIN orders o ON o.client_id = ec.user_id
       AND o.event_id = ec.event_id
       AND o.client_type != ?
   JOIN order_cart_service ocs ON ocs.order_id = o.id
   JOIN event_sellable_service ess ON ess.id = ocs.service_id
   WHERE ec.event_id = ?
       AND ocs.cancelled_at IS NULL
       AND ess.service_group IS NOT NULL

   UNION

   -- Get contacts who were attributed services
   SELECT DISTINCT
       oa.event_contact_id as contact_id,
       ess.service_group as family_id
   FROM order_attributions oa
   JOIN orders o ON o.id = oa.order_id
   JOIN order_cart_service ocs ON ocs.id = oa.shoppable_id
       AND oa.shoppable_type = ?
   JOIN event_sellable_service ess ON ess.id = ocs.service_id
   WHERE o.event_id = ?
       AND ocs.cancelled_at IS NULL
       AND ess.service_group IS NOT NULL
),
active_families AS (
   -- Get families that are active for this event
   SELECT DISTINCT es.service_id as family_id
   FROM event_service es
   WHERE es.event_id = ?
)
SELECT
   de.id,
   de.position,
   de.name,
   COUNT(DISTINCT sc.contact_id) as contact_count,
   CASE WHEN af.family_id IS NOT NULL THEN 1 ELSE 0 END as is_active
FROM dictionnary_entries de
INNER JOIN dictionnaries d ON d.id = de.dictionnary_id
LEFT JOIN service_contacts sc ON sc.family_id = de.id
LEFT JOIN active_families af ON af.family_id = de.id
WHERE d.slug = ?
   AND de.deleted_at IS NULL
GROUP BY de.id, de.position, de.name, af.family_id
ORDER BY de.position, JSON_UNQUOTE(JSON_EXTRACT(de.name, '$.fr'))
",
            [
                OrderClientType::GROUP->value,
                $this->event->id,
                OrderCartType::SERVICE->value,
                $this->event->id,
                $this->event->id,
                'service_family',
            ],
        );

        return [
            'data' => $this->queryResponse,
        ];
    }
}
