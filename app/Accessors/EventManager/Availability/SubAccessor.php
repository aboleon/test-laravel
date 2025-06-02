<?php

namespace App\Accessors\EventManager\Availability;

use App\Accessors\EventManager\Availability;
use Illuminate\Support\Carbon;

abstract class SubAccessor
{
    protected int $group_id;
    protected int $base_group_id;
    protected int $participation_type;

    public function __construct(public Availability $availability)
    {
        $this->group_id           = $this->availability->getEventGroupId();
        $this->base_group_id      = $this->availability->baseGroupId();
        $this->participation_type = $this->availability->getParticipationType();

        $this->availability->generateData();
    }

    abstract public function get(): array;

    abstract protected function generate(string $date, ?int $roomgroup=null): array;

    abstract protected function compose(): void;

    protected function autoParseDate(?string $date = null): ?string
    {
        if ( ! $date) {
            return null;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            $date = Carbon::createFromFormat('d/m/Y', $date)->toDateString();
        } elseif ( ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = null;
        }

        return $date;
    }

}
