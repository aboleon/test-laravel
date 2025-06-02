<?php

namespace App\Helpers\Price;

use App\Models\Vat;

class DivinePrice
{

    private float $net;
    private float $vat;
    private float $pec;

    public function __construct(array $priceInfo = [])
    {
        $this->net = $priceInfo['net'] ?? 0;
        $this->vat = $priceInfo['vat'] ?? 0;
        $this->pec = $priceInfo['pec'] ?? 0;
    }

    public function net(): float
    {
        return $this->net;
    }

    public function vat(): float
    {
        return $this->vat;
    }

    public function pec(): float
    {
        return $this->pec;
    }

    public function ttc(): float
    {
        return $this->net + $this->vat;
    }
}
