<?php

namespace App\Dashboards\Queries;

use App\Enum\OrderClientType;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

/**
 * Toutes les commandes de type 'orator'
 */
class DashboardPecOrdersQuery
{
    use EventModelTrait;

    public function run()
    {

        return
            DB::select(
                "SELECT
         FORMAT(COALESCE(SUM(CASE WHEN src='service' THEN amt END),0)/100,2,'fr_FR')          AS total_services,
         FORMAT(COALESCE(SUM(CASE WHEN src IN ('accommodation','taxroom') THEN amt END),0)/100,2,'fr_FR') AS total_accommodation,
         FORMAT(COALESCE(SUM(amt),0)/100,2,'fr_FR')                                           AS total_orders
     FROM (
         /* services ‑ pec ≠ 0 */
         SELECT 'service' AS src,
                SUM(ocs.total_net + ocs.total_pec) AS amt
         FROM   orders o
         JOIN   order_cart_service ocs ON ocs.order_id = o.id
         WHERE  o.event_id = ?                     -- event id
           AND  ocs.cancelled_at IS NULL
           AND  ocs.total_pec <> 0                -- keep only rows with pec

         UNION ALL
         /* accommodation ‑ pec ≠ 0 */
         SELECT 'accommodation',
                SUM(oca.total_net + oca.total_pec)
         FROM   orders o
         JOIN   order_cart_accommodation oca ON oca.order_id = o.id
         WHERE  o.event_id = ?
           AND  oca.cancelled_at IS NULL
           AND  oca.total_pec <> 0

         UNION ALL
         /* tourist‑tax room ‑ pec ≠ 0 */
         SELECT 'taxroom',
                SUM(oct.amount_net + oct.amount_pec)
         FROM   orders o
         JOIN   order_cart_taxroom oct ON oct.order_id = o.id
         WHERE  o.event_id = ?
           AND  oct.amount_pec <> 0
     ) x",
                [
                    $this->event->id,   // for services
                    $this->event->id,   // for accommodation
                    $this->event->id,   // for tax‑room
                ],
            )[0];
    }
}
