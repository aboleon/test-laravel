<?php

namespace App\Dashboards\Queries;

use App\Dashboards\Traits\DashboardTrait;
use App\Enum\OrderType;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

class EventContactsWhitoutAnyOrderQuery
{
    use EventModelTrait;
    use DashboardTrait;

    public function run(): array
    {
        $count = DB::selectOne(
            "
    SELECT COUNT(DISTINCT ec.id) as total
    FROM events_contacts ec
    WHERE ec.event_id = ?
    AND NOT EXISTS (
        SELECT 1
        FROM orders o
        WHERE o.client_id = ec.user_id
        AND o.event_id = ec.event_id
        AND o.type = ?
    )
    AND NOT EXISTS (
        SELECT 1
        FROM order_attributions oa
        JOIN orders o ON oa.order_id = o.id
        WHERE oa.event_contact_id = ec.id
        AND o.event_id = ec.event_id
    )
",
            [
                $this->event->id,
                OrderType::ORDER->value
            ],
        )->total;

        return [
            'total' => $count,
        ];
    }
}
