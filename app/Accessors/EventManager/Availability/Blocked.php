<?php

namespace App\Accessors\EventManager\Availability;

use App\Accessors\EventManager\Availability;

class Blocked extends SubAccessor
{
    private array $data = [];

    public function __construct(public Availability $availability)
    {
        parent::__construct($this->availability);
        $this->compose();
    }

    public function get(?string $date = null, ?int $roomgroup = null): array
    {
        if ($date && !$roomgroup) {
            return $this->data[$date] ?? [];
        }

        if ($date && $roomgroup) {
            return $this->data[$date][$roomgroup] ?? [];
        }

        return $this->data;
    }

    protected function generate(string $date, ?int $roomgroup = null): array
    {

        return [
            'date' => $date,
            'roomgroup' => $roomgroup,
            'invividual' => $this->availability->get('blocked')['individual'][$date][$roomgroup] ?? null,
            'invividual_ptype' => collect($this->availability->get('blocked')['individual_by_participation_type'])
                ->mapWithKeys(fn($item, $key) => isset($item[$date][$roomgroup]) ? [$key => $item[$date][$roomgroup]] : []
                )->all(),
            'groups_event_group_id' => collect($this->availability->get('blocked')['groups_by_event_group_id'])
                ->mapWithKeys(fn($item, $key) => isset($item[$date][$roomgroup]) ? [$key => $item[$date][$roomgroup]] : []
                )->all(),
            'groups_base_group_id' => collect($this->availability->get('blocked')['groups_by_event_group_id'])
                ->mapWithKeys(fn($item, $key) => isset($item[$date][$roomgroup]) ? [$this->availability->baseGroupId($key) => $item[$date][$roomgroup]] : []
                )->all(),
        ];

    }

    protected function compose(): void
    {
        foreach ($this->availability->get('contingent') as $date => $contingent) {
            foreach ($contingent as $roomgroup => $stock) {
                $this->data[$date][$roomgroup] = $this->generate($date, $roomgroup);
            }
        }
    }
}
