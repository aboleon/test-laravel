<?php

namespace App\Dashboards;

use App\Dashboards\Queries\DashboardOneQuery;
use App\Dashboards\Queries\DashboardOratorsQuery;
use App\Dashboards\Queries\DashboardOrdersPecOnlyByGroupQuery;
use App\Dashboards\Queries\DashboardOrdersWithoutPecByGroupQuery;
use App\Dashboards\Queries\DashboardPecOrdersQuery;
use App\Dashboards\Traits\DashboardTrait;
use App\Enum\ParticipantType;
use App\Models\Event;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use MetaFramework\Accessors\Prices;
use stdClass;

class OrdersDashboard
{
    use DashboardTrait;

    public readonly Event $event;

    public function sum($array, $column)
    {
        return Prices::readableFormat(array_sum(array_column($array, $column)) / 100);
    }

    public function format($value)
    {
        return Prices::readableFormat($value / 100);
    }

    public function dashboard(Event $event): Renderable
    {
        $this->event = $event;
        $group_types = $this->getGroups();
        array_unshift($group_types, 'all');

        return view('orders.dashboard')->with([
            'instance'         =>$this,
            'event'            => $this->event,
            'groups'           => $group_types,
            'global'           => $this->getGlobal(),
            'orators'          => $this->getOratorOrders(),
            'pec'              => $this->getPecOrders(),
            'non_pec_by_group' => $this->getNotPecByGroup(),
            'pec_by_group'     => $this->getPecByGroup(),
        ]);
    }

    private function getGlobal()
    {
        return (new DashboardOneQuery())->setEvent($this->event)->run();
    }

    private function getOratorOrders()
    {
        return (new DashboardOratorsQuery())->setEvent($this->event)->run();
    }

    private function getPecOrders()
    {
        return (new DashboardPecOrdersQuery())->setEvent($this->event)->run();
    }

    private function getNotPecByGroup()
    {
        return (new DashboardOrdersWithoutPecByGroupQuery())->setEvent($this->event)->run();
    }

    private function getPecByGroup()
    {
        return (new DashboardOrdersPecOnlyByGroupQuery())->setEvent($this->event)->run();
    }


}
