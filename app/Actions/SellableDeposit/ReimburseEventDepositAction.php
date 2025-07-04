<?php

namespace App\Actions\SellableDeposit;

use App\Actions\Ajax\AjaxAction;
use App\Enum\EventDepositStatus;
use App\Http\Controllers\MailController;
use App\Models\Order\EventDeposit;
use App\Models\Order\Refundable\RefundableDeposit;
use App\Services\PaymentProvider\PayBox\Paybox;
use Throwable;

class ReimburseEventDepositAction extends AjaxAction
{

    public function reimburseEventDeposit(): array
    {
        return $this->handle(function () {
            try {
                $eventDeposit = EventDeposit::findOrFail((int)request('id'));
            } catch (Throwable $e) {
                $this->responseException($e, "La caution à rembourser n'a pas pu être identifée.");

                return $this->fetchResponse();
            }

            $reimbursed = new Paybox()->sendReimbursementRequest(new RefundableDeposit($eventDeposit));

            if ($reimbursed->isSuccessful()) {
                $eventDeposit->reimbursed_at = now();
                $eventDeposit->status = EventDepositStatus::REFUNDED->value;
                $eventDeposit->save();
                $this->responseSuccess($reimbursed->responseComment());
            } else {
                $this->responseError("Le remboursement a échoué avec un code ".$reimbursed->responseCode()." - ".$reimbursed->responseComment());
            }

            if ( ! $this->hasErrors()) {
                $mc = new MailController();
                $mc->ajaxMode()->distribute('EventDepositReimbursementNotice', $eventDeposit);

                if ($mc->hasErrors()) {
                    $this->responseError("L'e-mail de notification du remborusement n'a pas pu être envoyé.");
                }

                $this->pushMessages($mc);
            }

            return $this->fetchResponse();
        });
    }

}
