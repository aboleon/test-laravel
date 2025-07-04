<?php

namespace App\Models\Order\Refundable;

use App\Interfaces\RefundableInterface;
use App\Models\EventManager\Sellable;
use App\Models\FrontTransaction;
use App\Models\Order;
use App\Models\Order\EventDeposit;
use App\Models\PaymentTransaction;
use MetaFramework\Accessors\VatAccessor;

class RefundableDeposit implements RefundableInterface
{
    private string $refundableReason;
    private int $vat_id;

    public function __construct(public readonly EventDeposit $deposit)
    {
        $this->vat_id           = $deposit->vat_id ?: VatAccessor::defaultId();
        $this->refundableReason = __('front/order.deposit_reimbursement');
    }

    public function refundableAmount(): int
    {
        return $this->deposit->total_net + $this->deposit->total_vat;
    }

    public function normalizedAmount(): int
    {
        return $this->deposit->getRawOriginal('total_net') + $this->deposit->getRawOriginal('total_vat');
    }

    public function vatId(): int
    {
        return $this->vat_id;
    }

    public function refundableReason(): string
    {
        return $this->refundableReason;
    }

    public function transaction(): FrontTransaction|PaymentTransaction
    {
        return match ($this->deposit->shoppable_type) {
            Sellable::class => $this->deposit->order->transaction,
            default => $this->deposit->paymentCall->transaction,
        };
    }

    public function model(): string
    {
        return EventDeposit::class;
    }

    public function id(): int
    {
        return $this->deposit->id;
    }

    public function order(): Order
    {
        return $this->deposit->order;
    }
}
