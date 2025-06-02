<?php

namespace App\Livewire\Front\Cart;

use App\Accessors\EventAccessor;
use App\Accessors\EventContactAccessor;
use App\Accessors\Front\FrontCache;
use App\Accessors\Front\FrontCartAccessor;
use App\Actions\Front\Cart\FrontCartActions;
use App\Actions\Front\Transaction;
use App\Actions\Order\PecActionsFront;
use App\Http\Controllers\Front\Paybox\PayboxController;
use App\Models\EventContact;
use App\Models\FrontCartLine;
use App\Models\PaymentCall;
use App\Services\PaymentProvider\PayBox\Paybox;
use Carbon\Carbon;
use Livewire\Component;

class PopupCart extends Component
{

    public EventContact $eventContact;


    public string $page = 'cart';
    public string $finalizePaymentError = "";

    public bool $showCart = false;
    public Carbon $expirationTime;
    public bool $showQuantities = false;

    //public FrontCartAccessor $cart;

    public int $count = 0;
    public float $itemsTotalTtc = 0;
    public int|float $amendableAmount = 0;
    public float $itemsTotalPec = 0;
    public float $itemsTotalNet = 0;
    public float $servicesTotalTtc = 0;
    public float $staysTotalTtc = 0;
    public float $nonTaxableTotalTtc = 0;
    public array $cartLines = [];


    public bool $paymentSuccessful = false;
    public ?string $paymentError = null;

    public string $payboxFormBegin = '';
    public string $payboxFormEnd = '';
    public bool $payboxServerOk = true;


    public bool $isPecEligible = false;
    public bool $isPecFinanceable = false;
    public bool $isGroupManager = false;

    protected PecActionsFront $pec;


    protected $listeners
        = [
            'PopupCart.refresh' => 'refresh',
        ];

    public ?EventContact $groupManager;

    public function __construct()
    {
        $this->groupManager = FrontCache::getGroupManager();
    }


    public function mount(EventContact $eventContact, bool $isGroupManager = false)
    {
        $this->eventContact   = $eventContact;
        $this->isGroupManager = $isGroupManager;
        $eventContactAccessor = (new EventContactAccessor())->setEventContact($eventContact);


        $this->isPecEligible = $eventContactAccessor->isPecAuthorized();



        $this->refresh();
        $this->mountAfter();
    }


    protected function mountAfter()
    {
        // override me in subclasses.
    }


    public function render()
    {
        return view('livewire.front.cart.popup-cart');
    }


    public function refresh(bool $showCart = false)
    {
        $cart = new FrontCartAccessor();
        $cart->fetchCart();

        $this->cartLines          = $cart->getLines();
        $this->itemsTotalTtc      = $cart->getTotalTtc();
        $this->amendableAmount    = $cart->getAmendableAmount();
        $this->itemsTotalNet      = $cart->getTotalNet();
        $this->itemsTotalPec      = $cart->getTotalPec();
        $this->servicesTotalTtc   = $cart->getServicesTotalTtc();
        $this->staysTotalTtc      = $cart->getStaysTotalTtc();
        $this->nonTaxableTotalTtc = $cart->getNonTaxableTotalTtc();
        $this->count              = $this->countItems($this->cartLines);
        $this->showCart           = $showCart;
        $this->expirationTime     = $cart->getExpirationTime();
    }

    public function updateServiceQuantity(int $serviceId, int $quantity): void
    {
        $cart     = new FrontCartActions();
        $response = $cart->updateServiceQuantity($serviceId, $quantity);
        if (array_key_exists("confirm", $response)) {
            $this->dispatch('PopupCart.confirmUpdateServiceQuantity', [
                'message'   => $response['confirm'],
                'serviceId' => $serviceId,
                'quantity'  => $quantity,
            ]);
        } else {
            $this->dispatch('PopupCart.updateServiceQuantityAfter', ...$response);
            $this->refresh(true);
        }
    }

    public function removeService(int $serviceId)
    {
        $this->updateServiceQuantity($serviceId, 0);
    }

    public function removeStay(FrontCartLine $frontCartLine)
    {
        FrontCartActions::deleteCartLine($frontCartLine);
        $this->refresh(true);
    }

    public function removeWaiverFee()
    {
        $grantWaiverFeesLines = $this->cartLines['grantWaiverFees'];
        $grantWaiverFeesLines->each(function ($line) {
            FrontCartActions::deleteCartLine($line);
        });
        $this->refresh(true);
    }


    public function recheckPreorder()
    {
        $this->dispatch('PopupCart.confirmRecheckPreorderSuccess');
    }

    public function finalizePayment(): void
    {
        $this->processPec();

        if ($this->isPecEligible && ! $this->pec->hasPossiblePec()) {
            $adminSubEmail              = EventAccessor::getAdminSubscriptionEmail($this->eventContact->event);
            $this->finalizePaymentError = __('front/cart.pec_uneligible', ['email' => $adminSubEmail]);

            return;
        }

        # Process PEC
        $cartAccessor = new FrontCartAccessor();
        $cartAccessor->getCart();

        if ($this->isPecFinanceable) {
            $cartAccessor->getCart()->update([
                'pec_eligible' => 1,
                'pec'          => serialize($this->pec),
            ]);

            $this->pec->setFrontCartId($cartAccessor->getCart()->id);

            $this->pec
                ->registerPecDistributionResult()
                ->registerQuotas();
        }

        # Generate payement call
        $paymentCall = Transaction::createPaymentCall($cartAccessor, new Paybox());

        if ($cartAccessor->getPayableAmount() > 0) {
            $this->page            = "payment";
            $paybox                = (new Paybox())->setOrderable($paymentCall);
            $serverOk              = true;
            $this->payboxFormBegin = $paybox->renderPaymentFormBegin($serverOk);
            $this->payboxServerOk  = $serverOk;
            $this->payboxFormEnd   = $paybox->renderPaymentFormEnd();
        } else {
            $this->processDischargedFromPayment($paymentCall);
        }
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function countItems(array $cartLines): int
    {
        $c = 0;
        foreach ($cartLines as $collection) {
            foreach ($collection as $model) {
                $c += $model->quantity;
            }
        }

        return $c;
    }

    private function processDischargedFromPayment(PaymentCall $paymentCall): void
    {
        $this->page = "pay_result";

        $paybox                  = (new PayboxController())->processDischargedFromPayment($paymentCall);
        $this->paymentSuccessful = ! $paybox->hasErrors();

        if ($paybox->hasErrors()) {
            $this->paymentError = __('front/order.unable_to_process_order');
        }
    }

    private function processPec(): void
    {
        if ($this->isPecEligible) {
            $this->pec = (new PecActionsFront());

            // PEC check

            $this->pec
                ->setEvent($this->eventContact->event)
                ->setEventContact($this->eventContact)
                ->pecParser();

            if ($this->pec->hasPossiblePec()) {
                $this->pec
                    ->parseFrontBookedServices($this->cartLines['services'])
                    ->parseFrontBookedAccomodation($this->cartLines['stays'])
                    ->findPec();

                if ($this->pec->getPecDistributionResult()->isCovered()) {
                    $this->isPecFinanceable = true;
                }
            }
        }
    }
}
