<?php

namespace App\Accessors\Front;

use App\Enum\OrderType;
use App\Http\Controllers\Front\Auth\AuthenticatedSessionController;
use App\Models\EventContact;
use App\Models\EventManager\Sellable;
use App\Models\FrontCart;
use App\Models\FrontCartLine;
use App\Models\PaymentCall;
use App\Traits\CheckParameters;
use App\Traits\Front\Cart\FrontCartTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use MetaFramework\Accessors\VatAccessor;
use MetaFramework\Traits\Responses;
use Throwable;

class FrontCartAccessor
{
    use Responses;
    use FrontCartTrait;
    use CheckParameters;

    private ?Collection $cartLines = null;

    public const ORDER_TTL_SECONDS = 30 * 60;

    private ?FrontCart $cart = null;
    private ?EventContact $eventContact;

    public function __construct()
    {
        $this->eventContact = FrontCache::getEventContact();
    }

    public function setCart(FrontCart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }


    public function getCart(): FrontCart
    {
        $this->fetchCart();

        return $this->cart;
    }

    public function fetchCart(): self|RedirectResponse
    {
        if ($this->cart !== null) {
            return $this;
        }

        $ec     = FrontCache::getEventContact();
        $sudoer = FrontCache::getGroupManager();
        [$cart, $groupCart] = $this->findCart($sudoer);

        try {
            if ( ! $cart && ! $groupCart && $ec) {
                $cart                                 = new FrontCart();
                $cart->event_contact_id               = $ec->id;
                $cart->group_manager_event_contact_id = $sudoer?->id;
                $cart->save();
            } else {
                $cart = $cart ?? $groupCart;
            }

            $this->cart = $cart->load('lines');


            return $this;
        } catch (Throwable) {
            Log::info("Destoyed the session from fetchCart in FrontCartAccessor");
            return (new AuthenticatedSessionController())->destroy(request());
        }
    }

    private function findCart($sudoer): array
    {
        $ec        = FrontCache::getEventContact();
        $cartQuery = FrontCart::query()->whereNull('order_id');
        $cart      = $cartQuery->where('event_contact_id', $ec->id)->first();

        $groupCart = null;
        if ($sudoer) {
            $cart = $cartQuery->where('group_manager_event_contact_id', $sudoer->id)->first();
        } else {
            $groupCart = $cartQuery->where('group_manager_event_contact_id', $ec->id)->first();
        }

        $cart      = $this->handleCartExpiration($cart);
        $groupCart = $this->handleCartExpiration($groupCart);

        return [$cart, $groupCart];
    }

    private function handleCartExpiration(?FrontCart $cart): ?FrontCart
    {
        if (! $cart) {
            return null;
        }

        /** 1️⃣ cart has a closed payment call but isn’t linked to the order yet */
        $paymentCall = $cart->paymentCall;
        if ($paymentCall
            && $paymentCall->closed_at !== null
            && $cart->order_id === null
            && $paymentCall->order_id !== null) {

            $cart->order_id = $paymentCall->order_id;
            $cart->save();

            return null;
        }

        /** 2️⃣ regular TTL logic (unchanged) */
        if (! $paymentCall) {
            $elapsed = $cart->updated_at->diffInSeconds(now());
            if ($elapsed > self::ORDER_TTL_SECONDS) {
                $cart->delete();
                return null;
            }

            $cart->touch();
        }

        return $cart;
    }


    public function paymentCall(): ?PaymentCall
    {
        return $this->cart->paymentCall;
    }

    public function getServiceLines(): Collection
    {
        return $this->getCartLines()->where('shoppable_type', Sellable::class);
    }

    public function getGrantWaiverFeeLines(): Collection
    {
        return $this->getCartLines()->where('shoppable_type', OrderType::GRANTDEPOSIT->value);
    }

    public function getStayLines(): Collection
    {
        return $this->getCartLines()->where('shoppable_type', "stay");
    }

    public function getServiceLineByServiceId(int $serviceId): ?FrontCartLine
    {
        return $this->getCartLines()->where('shoppable_type', Sellable::class)->where('shoppable_id', $serviceId)->first();
    }

    public function isEmpty(): bool
    {
        return $this->getCartLines()->isEmpty();
    }

    public function getTotalTtc(): int|float
    {
        return $this->sumCartLines('total_ttc') - $this->getAmendableAmount() - $this->getExcludableProcessingFees();
    }

    public function getAmendableAmount(): int|float
    {
        return $this->getCartLines()->reduce(fn($carry, $item) => $carry + ($item['meta_info']['amendable_amount'] ?? 0), 0);
    }

    public function getAmendableVatAmount(): int|float
    {
        return $this->getCartLines()->reduce(fn($carry, $item) => $carry + (! empty($item['meta_info']['amendable_amount']) ? VatAccessor::vatForPrice($item['meta_info']['amendable_amount'], $item->vat_id) : 0), 0);
    }

    public function getAmendableNetAmount(): int|float
    {
        return $this->getCartLines()->reduce(fn($carry, $item) => $carry + (! empty($item['meta_info']['amendable_amount']) ? VatAccessor::netPriceFromVatPrice($item['meta_info']['amendable_amount'], $item->vat_id) : 0), 0);
    }

    public function getExcludableProcessingFees(): int|float
    {
        return $this->getCartLines()->reduce(fn($carry, $item) => $carry + (! empty($item['meta_info']['amendable_amount']) ? $item['meta_info']['processing_fee_ttc'] : 0), 0);
    }

    public function getTotalNet(): int|float
    {
        $total = 0;
        foreach ($this->getCartLines() as $line) {
            $has_amendable   = ! empty($line['meta_info']['amendable_amount']);
            $processing_fees = ($has_amendable ? $line['meta_info']['processing_fee_ttc'] : 0);
            $amount          = $line->total_ttc - $line->total_pec -($has_amendable ? $line['meta_info']['processing_fee_ttc'] : 0);
            $total           += $amount > 0 ? VatAccessor::netPriceFromVatPrice($amount, $line->vat_id) : 0;
            if ($processing_fees) {
                $total += $line['meta_info']['processing_fee_vat'];
            }
            if ($has_amendable) {
                $total -= VatAccessor::netPriceFromVatPrice($line['meta_info']['amendable_amount'], $line->vat_id);
            }
        }

        return $total;
    }

    public function getPayableAmount(): int|float
    {
        return $this->getTotalTtc() - $this->getTotalPec();
    }

    public function getTotalPec(): int|float
    {
        return $this->sumCartLines('total_pec');
    }

    private function sumCartLines(string $column): int|float
    {
        return $this->getCartLines()->sum(fn($line) => $line->{$column});
    }

    public function getCartLines(): Collection
    {
        // TODO
        /*
         * Vérifier pourquoi l'original du code à mené à une fatal error
         * probablement en raison de l'initiation du cart par défaut en front
         * if ($this->cartLines === null) {
            $this->cartLines = $this->cart->lines;
        }
         * */
        if ($this->cartLines === null && $this->cart) {
            $this->cartLines = $this->cart->lines;
        }

        if ($this->cartLines === null) {
            $this->cartLines = new Collection();
        }

        return $this->cartLines;
    }

    public function getExpirationTime(): Carbon
    {
        return $this->cart->updated_at->addSeconds(self::ORDER_TTL_SECONDS);
    }

    public function getServicesTotalTtc(): float
    {
        return round($this->getCartLines()->where('shoppable_type', Sellable::class)->sum('total_ttc') / 100, 2);
    }

    public function getNonTaxableTotalTtc(): float
    {
        return $this->getCartLines()->sum(function ($line) {
            if (Sellable::class === $line->shoppable_type && $line->meta_info && array_key_exists("deposit_ttc", $line->meta_info)) {
                return $line->meta_info['deposit_ttc'];
            }

            return 0;
        });
    }

    public function getStaysTotalTtc(): float
    {
        return round($this->getCartLines()->where('shoppable_type', "stay")->sum('total_ttc') / 100, 2);
    }

    public function getLines(): array
    {
        $services        = $this->getServiceLines();
        $stays           = $this->getStayLines();
        $grantWaiverFees = $this->getGrantWaiverFeeLines();

        return [
            'services'        => $services,
            'stays'           => $stays,
            'grantWaiverFees' => $grantWaiverFees,
        ];
    }

    public function hasSellable(Sellable $sellable): bool
    {
        return $this->getCartLines()->where('shoppable_type', Sellable::class)->where('shoppable_id', $sellable->id)->count();
    }


}
