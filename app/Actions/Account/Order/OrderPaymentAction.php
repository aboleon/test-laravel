<?php

namespace App\Actions\Account\Order;

use App\Accessors\OrderAccessor;
use App\Enum\OrderStatus;
use App\Http\Requests\PaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use MetaFramework\Services\Validation\ValidationTrait;

class OrderPaymentAction
{

    use ValidationTrait;

    private int $order_id;

    private ?Order $order = null;

    private ?Payment $payment = null;

    public function __construct()
    {
        $this->responseElement('callback', 'manageInvoiceableStatus');
    }

    public function savePayment(array $data, int $id): array
    {
        $this->enableAjaxMode();

        $validation                = (new PaymentRequest());
        $this->validation_rules    = $validation->rules();
        $this->validation_messages = $validation->messages();
        $this->validation();

        if ( ! array_key_exists('order_id', $data)) {
            $this->responseError('Numéro de commande manquant');

            return $this->fetchResponse();
        }

        $this->order_id = $data['order_id'];
        $this->payment  = $id !== 0 ? Payment::findOrFail($id) : (Payment::find($this->order_id) ?: new Payment());

        $this->order   = Order::findOrFail($this->order_id);
        $orderAccessor = (new OrderAccessor($this->order));
        $orderTotal    = $orderAccessor->getTotal();

        $total_paid   = $this->getTotal();
        $newTotalPaid = $total_paid - $this->payment->amount + $this->validatedData('amount');

        if ($newTotalPaid > $orderTotal) {
            $this->responseError("Le montant total des paiements dépasse le montant total de la commande.");
            $this->responseElement('newTotalPaid',$newTotalPaid);
            $this->responseElement('orderTotal',$orderTotal);

            return $this->fetchResponse();
        }

        $this->payment->order_id             = $data['order_id'];
        $this->payment->date                 = $data['date'] ?? date('Y-m-d');
        $this->payment->amount               = $this->validatedData('amount');
        $this->payment->payment_method       = $data['payment_method'] ?? null;
        $this->payment->authorization_number = $data['authorization_number'] ?? null;
        $this->payment->card_number          = $data['card_number'] ?? null;
        $this->payment->bank                 = $data['bank'] ?? null;
        $this->payment->issuer               = $data['issuer'] ?? null;
        $this->payment->check_number         = $data['check_number'] ?? null;

        $this->payment->save();

        $this->responseSuccess('Paiement sauvegardé');
        $this->responseElement('paid', $this->getTotal());
        $this->responseElement('id', $this->payment->id);

        $this->updateOrderStatus($newTotalPaid);

        return $this->fetchResponse();
    }

    public function deletePayment(int $id): array
    {
        $this->enableAjaxMode();
        $this->payment = Payment::find($id);


        if ($this->payment) {
            $this->order  = $this->payment->order;
            $newTotalPaid = $this->getTotal() - $this->payment->amount;

            $this->payment->delete();

            // Check if order is still paid
            $this->updateOrderStatus($newTotalPaid);

            $this->responseSuccess('Paiement supprimé');
            $this->responseElement('paid', $this->getTotal());
        } else {
            $this->responseError('Paiement introuvable');
        }

        return $this->fetchResponse();
    }

    public function getTotal(): int|float
    {
        return Payment::where('order_id', $this->order->id)->sum('amount') / 100;
    }

    private function updateOrderStatus(int|float $newTotalPaid): void
    {
        $orderAccessor = (new OrderAccessor($this->order));
        $orderTotal    = $orderAccessor->getTotal();
        $oldStatus     = $this->order->status;

        $orderStatus = $orderTotal == $newTotalPaid ? OrderStatus::PAID->value : OrderStatus::UNPAID->value;


        $this->order->status = $orderStatus;
        $this->order->save();

        if ($orderStatus == OrderStatus::PAID->value) {
            $this->responseSuccess("La commande est soldée.");
        } else {
            $this->responseWarning("La commande n'est pas soldée.", error: false);
        }


        if ($oldStatus != $orderStatus) {
            $this->responseNotice("Cette page va recharger dans 3 secondes...");
        }

        $this->responseElement('old_status', $oldStatus);
        $this->responseElement('new_status', $orderStatus);
    }
}
