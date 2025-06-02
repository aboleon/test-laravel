<?php

namespace App\Actions\Refunds;

use App\Models\Order\Refundable\RefundablePayment;
use App\Models\Payment;
use App\Services\PaymentProvider\PayBox\Paybox;
use MetaFramework\Traits\Ajax;
use Throwable;

class RefundFrontTransactionAction
{
    use Ajax;

    public function reimburseFrontPayment(): array
    {
        $validatedData = request()->validate([
            'reason' => 'required|string',
            'amount' => 'required|numeric',
            'vat'    => 'required|integer',
        ], [
            'reason.required' => "L'intitulé pour le remboursement n'est pas indiqué",
            'reason.string'   => "L'intitulé pour le remboursement doit être un texte",
            'amount.required' => "Le montant du remboursement n'est pas indiqué",
            'amount.numeric'  => "Le montant du remboursement doit être un chiffre.",
            'vat.required'    => "Le taux de TVA n'est pas indiqué",
            'vat.integer'     => "Le taux de TVA n'est pas au bon format",
        ]);

        try {
            $payment = Payment::findOrFail((int)request('payment_id'));
        } catch (Throwable $e) {
            $this->responseException($e, "Le règlement à rembourser n'a pas pu être identifée.");

            return $this->fetchResponse();
        }

        $refundable = (new RefundablePayment($payment));
        $refundable->setVatId($validatedData['vat']);
        $refundable->setRefundableReason($validatedData['reason']);
        $refundable->setRefundableAmount($validatedData['amount']);

        $reimbursed = (new Paybox())->sendReimbursementRequest($refundable);

        if ($reimbursed->isSuccessful()) {
            $payment->reimbursed_at = now();
            $payment->log           = $validatedData;
            $this->responseSuccess($reimbursed->responseComment());
            $payment->save();

            // Generate refund slip / générer un avoir
            $this->pushMessages(
                (new RefundedTransactionDocumentAction($refundable))->shouldBeAjax($this->isAjaxMode())->create(),
            );
        } else {
            $error_msg = "Le remboursement a échoué avec un code ".$reimbursed->responseCode()." - ".$reimbursed->responseComment();
            $this->responseError($error_msg);
        }

        /*
         * Mail à envoyer ou non ?

        if ( ! $this->hasErrors()) {
            $mc = new MailController();
            $mc->ajaxMode()->distribute('PaymentReimbursementNotice', $payment);

            if ($mc->hasErrors()) {
                $this->responseError("L'e-mail de notification du remborusement n'a pas pu être envoyé.");
            }

            $this->pushMessages($mc);
        }
        */

        return $this->fetchResponse();
    }

}
