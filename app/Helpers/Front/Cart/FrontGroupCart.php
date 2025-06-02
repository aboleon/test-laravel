<?php

namespace App\Helpers\Front\Cart;

use App\Accessors\Front\FrontCartAccessor;
use App\Actions\Front\Cart\FrontCartActions;
use App\Models\EventContact;
use App\Models\EventManager\Sellable;
use App\Models\FrontCart;
use App\Traits\Front\Cart\FrontCartTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class FrontGroupCart implements FrontCartInterface
{

    use FrontCartTrait;

    private static $inst = null;


    private function __construct(
        private EventContact $groupManagerEventContact,
    ) {}

    public static function getInstance(EventContact $ec): FrontGroupCart
    {
        if (null === self::$inst) {
            self::$inst = new self($ec);
        }

        return self::$inst;
    }

    //--------------------------------------------
    // FrontCartInterface
    //--------------------------------------------
    public function isEmpty(): bool
    {
        $ret = true;
        self::getCarts()->each(function (FrontCart $frontCart) use (&$ret) {
            if ($frontCart->lines->count() > 0) {
                $ret = false;
            }
        });

        return $ret;
    }

    public function getTotalTtc(): float
    {
        return $this->getCarts()->sum(function ($cart) {
            return $cart->lines->sum(function ($line) {
                return $line->total_ttc;
            });
        });
    }

    public function getTotalNet(): float
    {
        return $this->getCarts()->sum(function ($cart) {
            return $cart->lines->sum(function ($line) {
                return $line->total_net;
            });
        });
    }

    public function clearCart(bool $replenishStock = true): void
    {
        FrontCart::setReplenishStock($replenishStock);
        $this->getCarts()->each(function (FrontCart $cart) {
            $cart->delete();
        });
        FrontCart::resetReplenishStock();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function getExpirationTime(): Carbon|null
    {
        $carts = $this->getCarts();
        if ($carts->isEmpty()) {
            return null;
        }

        return $carts->first()->updated_at->addSeconds(FrontCartAccessor::ORDER_TTL_SECONDS);
    }


    public function getCarts(bool $withLines = false): Collection
    {
        $q = FrontCart::query();
        if ($withLines) {
            $q->with("lines");
        }

        return $q
            ->where('group_manager_event_contact_id', $this->groupManagerEventContact->id)
            ->whereNull('order_id')
            ->get();
    }


    public function getServicesTotalTtc(): float
    {
        return $this->getCarts()->sum(function ($cart) {
            return $cart->lines()->where('shoppable_type', Sellable::class)->sum('total_ttc');
        });
    }

    public function getStaysTotalTtc(): float
    {
        return $this->getCarts()->sum(function ($cart) {
            return $cart->lines()->where('shoppable_type', "stay")->sum('total_ttc');
        });
    }


    public function getStayLines(): \Illuminate\Support\Collection
    {
        return $this->getCarts()->map(function ($cart) {
            return $cart->lines()->where('shoppable_type', "stay")->get();
        })->flatten();
    }

    public function getServiceLines(): \Illuminate\Support\Collection
    {
        return $this->getCarts()->map(function ($cart) {
            return $cart->lines()->where('shoppable_type', Sellable::class)->get();
        })->flatten();
    }


    public function removeService(int $serviceId, int $frontCartId, ?string &$error = null)
    {
        $cart    = FrontCart::findOrFail($frontCartId);
        $service = Sellable::findOrFail($serviceId);

        $this->checkCanRemoveService($service);
        if ($error) {
            return false;
        }
        $serviceLine = (new FrontCartAccessor())
            ->setCart($cart)->getServiceLineByServiceId($serviceId);

        if (!$serviceLine) {
            $error = "ServiceCart not found in cart";

            return false;
        }


        FrontCartActions::deleteCartLine($serviceLine);

        return true;
    }

}
