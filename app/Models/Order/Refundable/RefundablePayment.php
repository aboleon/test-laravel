<?php

namespace App\Models\Order\Refundable;

use App\Interfaces\RefundableInterface;
use App\Models\FrontTransaction;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;

class RefundablePayment implements RefundableInterface
{
    private string $refundableReason = '';
    private int $vat_id;

    private int $amount;

    public function __construct(public readonly Payment $payment) {
        $this->amount = $this->payment->getRawOriginal('amount');
    }

    public function refundableAmount(): int
    {
        return $this->amount;
    }

    public function normalizedAmount(): int
    {
        return $this->amount * 100;
    }

    public function setRefundableAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function vatId(): int
    {
        return $this->vat_id;
    }

    public function refundableReason(): string
    {
        return $this->refundableReason;
    }

    public function setRefundableReason(string $reason): self
    {
        $this->refundableReason = $reason;

        return $this;
    }

    public function setVatId(int $vatId): self
    {
        $this->vat_id = $vatId;

        return $this;
    }

    public function transaction(): FrontTransaction|PaymentTransaction
    {
        return $this->payment->transaction;
    }

    public function model(): string
    {
        return Payment::class;
    }

    public function id(): int
    {
        return $this->payment->id;
    }

    public function order(): Order
    {
        return $this->payment->order;
    }
}
