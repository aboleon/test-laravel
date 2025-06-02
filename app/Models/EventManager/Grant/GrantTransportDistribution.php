<?php

namespace App\Models\EventManager\Grant;

use App\Enum\DesiredTransportManagement;
use App\Traits\Models\EventContactModelTrait;
use App\Traits\Models\EventGrantModelTrait;
use App\Traits\Models\EventTransportModelTrait;
use Illuminate\Database\Eloquent\Model;
use MetaFramework\Casts\PriceInteger;

class GrantTransportDistribution extends Model
{

    use EventGrantModelTrait;
    use EventContactModelTrait;
    use EventTransportModelTrait;

    private int $cost = 0;
    private int $eventContactId;

    protected $casts = [
      'cost' => PriceInteger::class,
    ];

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function getManagementKey(): ?int
    {
        return DesiredTransportManagement::mapByKeyword($this->getEventTransport()->desired_management);
    }


}
