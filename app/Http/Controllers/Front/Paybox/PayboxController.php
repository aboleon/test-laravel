<?php

namespace App\Http\Controllers\Front\Paybox;

use App\Accessors\Front\FrontCache;
use App\Actions\Front\Order as FrontOrderAction;
use App\Actions\Front\Transaction;
use App\Http\Controllers\CustomPaymentController;
use App\Models\CustomPaymentCall;
use App\Models\PaymentCall;
use App\Services\PaymentProvider\PayBox\TransactionRequest;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use MetaFramework\Traits\Responses;
use Throwable;

class PayboxController
{
    use Responses;

    private null|PaymentCall|CustomPaymentCall $paymentCall = null;
    private null|FrontOrderAction $orderAction = null;


    /**
     * @throws Exception
     */
    public function rejected()
    {
        $response = [
            'return_code' => TransactionRequest::returnCode(),
            'return_type' => 'rejected',
        ];

        return redirect()->route("front.event.cart.edit", ['locale' => app()->getLocale(), 'event'  => FrontCache::getEventId()])->with($response);

    }

    /**
     * @throws Exception
     */
    public function cancelled()
    {
        $response = [
            'return_code' => TransactionRequest::returnCode(),
            'return_type' => 'cancelled',
        ];

        return redirect()->route("front.event.cart.edit", ['locale' => app()->getLocale(), 'event'  => FrontCache::getEventId()])->with($response);
    }

    /**
     * @throws Exception
     */
    public function success()
    {
        Log::info('Order Start');

        $response = [
            'return_code' => TransactionRequest::returnCode(),
            'return_type' => 'success',
        ];

        $this->identifyPaymentCall();

        if ( ! $this->paymentCall) {
            Log::info('Payment Call not found');
            $response = [
                'return_code' => TransactionRequest::returnCode(),
                'return_type' => 'rejected',
            ];

            return redirect()->route("front.event.cart.edit", ['locale' => app()->getLocale(), 'event'  => FrontCache::getEventId()])->with($response);
        }

        $this->processSuccessfullOrder();

        if ($this->paymentCall->isGroupManager()) {
            return redirect()->route("front.event.group.dashboard", [
                'locale' => app()->getLocale(),
                'event'  => $this->orderAction->getOrder()->event_id, // TODO : get event from cart
            ])->with($response);
        }

        return redirect()->route("front.event.cart.edit", [
            'locale' => app()->getLocale(),
            'event'  => (($this->paymentCall instanceof CustomPaymentCall) ?  $this->paymentCall->shoppable->event_id: $this->orderAction->getOrder()->event_id)
        ])->with($response);

    }

    private function processSuccessfullOrder()
    {
        DB::beginTransaction();

        try {
            Log::info('Start DB Transaction');
            //     try {
            if (TransactionRequest::successful()) {
                Log::info('Transaction is successful');
                // Close payment call
                $this->paymentCall->closed_at = now();
                $this->paymentCall->save();
            }

            if ($this->paymentCall instanceof CustomPaymentCall) {
                $process = new CustomPaymentController();
                try {
                    $process
                        ->setModel($this->paymentCall)
                        ->disableRedirect()
                        ->success();

                    DB::commit();
                } catch (Throwable $e) {
                    DB::rollback();
                    $this->responseException($e);
                }
            } else {
                Log::info('Transaction store');
                Transaction::storeTransactionResponse($this->paymentCall);

                $this->orderAction = (new FrontOrderAction(
                    paymentCall: $this->paymentCall,
                ));
                $this->paymentCall->isGroupManager()
                    ? $this->orderAction->processGroupManagerOrder()
                    : $this->orderAction->processIndividualOrder();


                DB::commit();
            }
        } catch (Throwable $e) {
            report($e);
            DB::rollback();
        }
    }

    /**
     * @throws Exception
     */
    public function autoresponse(): void
    {
        $this->identifyPaymentCall();

        if ( ! $this->paymentCall or ! is_null($this->paymentCall->closed_at)) {
            // Already processed
            return;
        }

        if (TransactionRequest::successful()) {
            $this->processSuccessfullOrder();
        }
    }

    protected function identifyPaymentCall(): void
    {
        Log::info('Payment Call identification');

        $this->paymentCall = PaymentCall::where('uuid', TransactionRequest::uuid())->first();

        if ( ! $this->paymentCall) {
            $this->paymentCall = CustomPaymentCall::where('uuid', TransactionRequest::uuid())->first();
        }
    }

    public function processDischargedFromPayment(
        PaymentCall $paymentCall,
    ) {
        DB::beginTransaction();

        try {
            $paymentCall->closed_at = now();
            $paymentCall->save();

            $orderAction = (new FrontOrderAction(
                paymentCall: $paymentCall,
            ));

            $paymentCall->isGroupManager()
                ? $orderAction->processGroupManagerOrder()
                : $orderAction->processIndividualOrder();

            DB::commit();
        } catch (Throwable $e) {
            $this->responseException($e);
            DB::rollback();
        }

        return $this;
    }
}
