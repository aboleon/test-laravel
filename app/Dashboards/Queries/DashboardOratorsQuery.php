<?php

namespace App\Dashboards\Queries;

use App\Enum\OrderClientType;
use App\Enum\OrderMarker;
use App\Enum\OrderType;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;

/**
 * Toutes les commandes de type 'orator'
 */
class DashboardOratorsQuery
{
    use EventModelTrait;

    public function run()
    {
        return DB::select(
            "SELECT
         FORMAT(COALESCE(SUM(CASE WHEN src='service'
                             THEN amt END),0)/100, 2, 'fr_FR')                AS total_services,
    FORMAT(COALESCE(SUM(CASE WHEN src IN ('accommodation','taxroom')
                             THEN amt END),0)/100, 2, 'fr_FR')                AS total_accommodation,
    FORMAT(COALESCE(SUM(amt),0)/100, 2, 'fr_FR')                              AS total_orders
     FROM (
         SELECT 'service' AS src,
                SUM(ocs.total_net + ocs.total_pec) AS amt
         FROM   orders o
         JOIN   order_cart_service ocs ON ocs.order_id = o.id
         WHERE  o.event_id = ? AND o.client_type = ? AND ocs.cancelled_at IS NULL
         UNION ALL
         SELECT 'accommodation',
                SUM(oca.total_net + oca.total_pec)
         FROM   orders o
         JOIN   order_cart_accommodation oca ON oca.order_id = o.id
         WHERE  o.event_id = ? AND o.client_type = ? AND oca.cancelled_at IS NULL
         UNION ALL
         SELECT 'taxroom',
                SUM(oct.amount_net + oct.amount_pec)
         FROM   orders o
         JOIN   order_cart_taxroom oct ON oct.order_id = o.id
         WHERE  o.event_id = ? AND o.client_type = ?
     ) x",
            [
                $this->event->id,
                OrderClientType::ORATOR->value,
                $this->event->id,
                OrderClientType::ORATOR->value,
                $this->event->id,
                OrderClientType::ORATOR->value,
            ],
        )[0];
    }
}
