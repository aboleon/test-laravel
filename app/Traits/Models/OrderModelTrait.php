<?php

namespace App\Traits\Models;

use App\Models\Order;
use MetaFramework\Services\Validation\ValidationModelPropertiesTrait;
use MetaFramework\Traits\Responses;

trait OrderModelTrait
{
    use Responses;
    use ValidationModelPropertiesTrait;

    protected ?Order $order = null;

    public function setOrder(null|int|Order $order): self
    {
        $this->order = is_int($order) ? Order::find($order) : $order;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

}
