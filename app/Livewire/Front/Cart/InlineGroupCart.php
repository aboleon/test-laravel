<?php

namespace App\Livewire\Front\Cart;


use App\Accessors\Front\FrontCartAccessor;
use App\Accessors\Front\FrontGroupCartAccessor;
use App\Actions\Front\Cart\FrontCartActions;
use App\Actions\Front\Transaction;
use App\Helpers\Front\Cart\FrontGroupCart;
use App\Models\EventContact;
use App\Models\EventManager\EventGroup;
use App\Models\FrontCartLine;
use App\Services\PaymentProvider\PayBox\Paybox;
use App\Traits\Livewire\LivewireModalTrait;
use Carbon\Carbon;
use Livewire\Component;

class InlineGroupCart extends Component
{

    use LivewireModalTrait;

    public EventContact $eventContact;
    public EventGroup $eventGroup;

    public string $page = 'cart';

    public float $servicesTotalTtc = 0;
    public float $staysTotalTtc = 0;
    public float $itemsTotalTtc = 0;
    public float $itemsTotalNet = 0;

    public Carbon $expirationTime;

    public bool $paymentSuccessful = false;
    public ?string $paymentError = null;

    public string $payboxFormBegin = '';
    public string $payboxFormEnd = '';
    public bool $payboxServerOk = true;


    public bool $isEligible = false;
    public string $finalizePaymentError = "";


    public function render()
    {
        return view('livewire.front.cart.inline-group-cart');
    }

    public function mount(EventContact $eventContact, EventGroup $eventGroup)
    {
        $this->eventContact = $eventContact;
        $this->eventGroup = $eventGroup;
        $this->refresh();
    }

    public function refresh(bool $showCart = false)
    {
        $groupCart = FrontGroupCart::getInstance($this->eventContact);
        $this->itemsTotalTtc = $groupCart->getTotalTtc();
        $this->itemsTotalNet = $groupCart->getTotalNet();
        $this->servicesTotalTtc = $groupCart->getServicesTotalTtc();
        $this->staysTotalTtc = $groupCart->getStaysTotalTtc();


        $carts = $groupCart->getCarts();
        $firstCart = $carts->first();
        if ($firstCart) {


            $elapsed = $firstCart->updated_at->diffInSeconds(Carbon::now());
            if ($elapsed > FrontCartAccessor::ORDER_TTL_SECONDS) {
                $groupCart->clearCart();
                return;
            }
            $groupCart->getCarts()->each(function ($cart) {
                $cart->updated_at = Carbon::now();
                $cart->save();
            });
            $this->expirationTime = $groupCart->getExpirationTime();
        }
    }

    public function recheckPreorder()
    {
        $this->dispatch('InlineGroupCart.confirmRecheckPreorderSuccess');
    }



    public function removeService(int $serviceId, int $frontCartId)
    {
        $groupCart = FrontGroupCart::getInstance($this->eventContact);

        $error = null;
        $groupCart->removeService($serviceId, $frontCartId, $error);
        if ($error) {
            $this->modalError($error);
            return;
        }

        $this->refresh();
    }

    public function removeStay(FrontCartLine $frontCartLine)
    {
        FrontCartActions::deleteCartLine($frontCartLine);
        $this->refresh(true);
    }


    public function finalizePayment()
    {
        $this->page = "payment";
        $cartAccessor = new FrontGroupCartAccessor();
        $paymentCall = Transaction::createGroupCartPaymentCall($cartAccessor, new Paybox());

        if ($paymentCall->total > 0) {
            $this->page            = "payment";
            $paybox                = (new Paybox())->setOrderable($paymentCall);
            $serverOk              = true;
            $this->payboxFormBegin = $paybox->renderPaymentFormBegin($serverOk);
            $this->payboxServerOk  = $serverOk;
            $this->payboxFormEnd   = $paybox->renderPaymentFormEnd();
        } else {
            // TODO
        }

    }
}
