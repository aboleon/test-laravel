<?php

namespace App\Livewire\Front\Cart;


use App\Models\FrontCart;

class InlineCart extends PopupCart
{
    public ?string $status = null;
    public FrontCart $cart;

    public function render()
    {
        return view('livewire.front.cart.inline-cart');
    }

    protected function mountAfter()
    {
        switch ($this->status) {
            case "paid":
                $this->page = "pay_result";
                $this->paymentSuccessful = true;
                break;
            case "cancelled":
                $this->page = "pay_result";
                $this->paymentSuccessful = false;
                $mail = $this->getAdminInscription();
                $this->paymentError = "Paiement annulé, merci de contacter $mail en cas de problème.";
                break;
            case "rejected":
                $mail = $this->getAdminInscription();
                $this->page = "pay_result";
                $this->paymentSuccessful = false;
                $this->paymentError = "Paiement refusé, merci de contacter $mail en cas de problème.";
                break;
            case "error":
                $mail = $this->getAdminInscription();
                $this->page = "pay_result";
                $this->paymentSuccessful = false;
                $this->paymentError = "Une erreur est survenue lors du paiement, merci de contacter $mail en cas de problème.";
                break;
        }
    }


    private function getAdminInscription()
    {
        return $this->eventContact->event->adminSubs ? $this->eventContact->event->adminSubs->email : $this->eventContact->event->admin->email;
    }


}
