<?php

namespace App\Actions\Refunds;

use App\Interfaces\RefundableInterface;
use App\Models\Order\Refund;
use App\Models\Order\RefundItem;
use MetaFramework\Traits\Ajax;
use Throwable;

class RefundedTransactionDocumentAction
{
    use Ajax;

    public function __construct(public RefundableInterface $refundable) {}

    public function create(): self
    {
        try {
            $refund = $this->refundable->order()->refunds()->save(new Refund(['payment_id' => $this->refundable->id()]));

            $refund->items()->save(new RefundItem([
                'object' => $this->refundable->refundableReason(),
                'amount' => $this->refundable->refundableAmount(),
                'vat_id' => $this->refundable->vatId(),
                'date'   => now()->toDateString(),
            ]));

            $this->responseSuccess("L'avoir correspondant a été généré.");

            $this->responseElement(
                'links',
                '<a class="mfw-edit-link btn btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
               data-bs-title="Éditer l\'avoir" href="'.route('panel.manager.event.refunds.edit', ['event' => $this->refundable->order()->event_id, 'refund' => $refund->id,]).'">
               <i class="fas fa-pen"></i></a>'.
                '<a href="'.route('pdf-printer', ['type' => 'refundable', 'identifier' => $refund->uuid]).'" class="mfw-edit-link btn btn-sm btn-danger" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PDF Avoir">
                <i class="fas fa-file-pdf"></i></a>',
            );
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this;
    }

}
