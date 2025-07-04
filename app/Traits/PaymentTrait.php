<?php

namespace App\Traits;

use Throwable;

trait PaymentTrait
{

    /**
     * @return string|null
     * Get stored Paybox card number
     */

    public function getCardNumber(): ?string
    {
        try {
            return $this->details['n'].'XXXXXX'.$this->details['j'];
        } catch (Throwable $th) {
            return null;
        }
    }
}
