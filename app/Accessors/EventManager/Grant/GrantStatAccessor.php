<?php

namespace App\Accessors\EventManager\Grant;

use App\Services\Pec\PecType;
use App\Traits\ModelSetters;
use Illuminate\Support\Facades\DB;

class GrantStatAccessor
{
    use ModelSetters;

    public function globalPecDistrubutionStats(): array
    {

        if (!$this->event) {
            return [];
        }

        return DB::select("SELECT
    pd.grant_id,
    CASE
        WHEN pd.type = 'taxroom' THEN 'accommodation'
        ELSE pd.type
    END AS type,
    FORMAT(
        SUM(
            CASE
                WHEN pd.type = 'transport' AND eg.amount_type = 'ht' THEN pd.unit_price * pd.quantity
                WHEN pd.type = 'transport' THEN pd.unit_price * pd.quantity
                ELSE pd.total_net + pd.total_vat
            END
        ) / 100, 2
    ) AS total_amount_formatted,
    FORMAT(
        SUM(
            CASE
                WHEN pd.type = 'transport' AND eg.amount_type = 'ht' THEN pd.unit_price * pd.quantity
                WHEN pd.type = 'transport' THEN 0
                ELSE pd.total_net
            END
        ) / 100, 2
    ) AS total_sub_ht_formatted,
    FORMAT(
        SUM(
            CASE
                WHEN pd.type = 'transport' THEN 0
                ELSE pd.total_vat
            END
        ) / 100, 2
    ) AS total_sub_vat_formatted,
    SUM(
        CASE
            WHEN pd.type = 'transport' AND eg.amount_type = 'ht' THEN pd.unit_price * pd.quantity
            WHEN pd.type = 'transport' THEN pd.unit_price * pd.quantity
            ELSE pd.total_net + pd.total_vat
        END
    ) AS total_amount,
    SUM(
        CASE
            WHEN pd.type = 'transport' AND eg.amount_type = 'ht' THEN pd.unit_price * pd.quantity
            WHEN pd.type = 'transport' THEN 0
            ELSE pd.total_net
        END
    ) AS total_sub_ht,
    SUM(
        CASE
            WHEN pd.type = 'transport' THEN 0
            ELSE pd.total_vat
        END
    ) AS total_sub_vat
FROM
    pec_distribution pd
JOIN
    event_grant eg ON eg.id = pd.grant_id
WHERE
    eg.event_id = " . $this->event->id . "
GROUP BY
    pd.grant_id,
    CASE
        WHEN pd.type = 'taxroom' THEN 'accommodation'
        ELSE pd.type
    END;

    ");
    }

    public function globalPecDistributionStatPosts(): array
    {
        return [
            PecType::PROCESSING_FEE->value,
            PecType::SERVICE->value,
            PecType::ACCOMMODATION->value,
            PecType::TRANSPORT->value,
        ];
    }

}
