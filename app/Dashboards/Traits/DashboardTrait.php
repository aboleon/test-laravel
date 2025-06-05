<?php

namespace App\Dashboards\Traits;

use App\Traits\Models\ParticipationGroupTypesTrait;
use stdClass;

trait DashboardTrait {

    use ParticipationGroupTypesTrait;

    protected array $queryResponse = [];


    public static function filterByGroup(array $data, string $group): bool|stdClass
    {
        $filtered = array_filter($data, fn($item) => $item->participation_group == $group);

        return ! empty($filtered) ? reset($filtered) : false;
    }

    public function getQueryResponse(): array
    {
        return $this->queryResponse;
    }
}
