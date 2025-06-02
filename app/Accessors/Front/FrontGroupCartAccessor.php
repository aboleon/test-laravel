<?php

namespace App\Accessors\Front;

use App\Accessors\EventManager\EventGroups;
use App\Actions\Front\Cart\FrontCartActions;
use App\Models\EventContact;
use App\Models\EventManager\EventGroup;
use App\Models\EventManager\Sellable;
use App\Models\FrontCart;
use App\Traits\Front\Cart\FrontCartTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class FrontGroupCartAccessor
{
    use FrontCartTrait;

    private ?EventContact $groupManager;
    private ?Collection $cachedCarts = null;

    /**
     * Private constructor to enforce singleton pattern
     */
    public function __construct()
    {
        $this->groupManager = FrontCache::getGroupManager();

        if ($this->groupManager === null) {
            $this->groupManager = FrontCache::forceSetGroupManager();
        }

        if ($this->groupManager === null) {
            throw new Exception('Front group manager not set');
        }
    }

    public function getGroupManager(): ?EventContact
    {
        return $this->groupManager;
    }

    public function getGroup(): ?EventGroup
    {
        return EventGroups::getGroupByMainContact(FrontCache::getEvent(), $this->groupManager->user);
    }

    /**
     * Fetch and cache group-specific carts to avoid redundant database queries.
     */
    public function getCarts(): Collection
    {
        // Check if the carts are already cached
        if ($this->cachedCarts === null) {
            $q = FrontCart::query();

            // Fetch group carts based on group_manager_event_contact_id and cache the result
            $this->cachedCarts = $q
                ->where('group_manager_event_contact_id', $this->groupManager->id)
                ->whereNull('order_id')
                ->get();
        }

        return $this->cachedCarts;
    }

    /**
     * Check if the cart is empty for group carts by using cached carts.
     */
    public function isEmpty(): bool
    {
        // Use cached carts
        return $this->getCarts()->filter(fn($cart) => $cart->lines->count() > 0)->isEmpty();
    }

    /**
     * Calculate total TTC for group carts using cached carts.
     */
    public function getTotalTtc(): float
    {
        // Use cached carts
        return $this->getCarts()->sum(function (FrontCart $cart) {
            return $cart->lines->sum('total_ttc');
        });
    }

    /**
     * Calculate total PEC for group carts using cached carts.
     */
    public function getTotalPec(): float
    {
        // Use cached carts
        return $this->getCarts()->sum(function (FrontCart $cart) {
            return $cart->lines->sum('total_pec');
        });
    }

    /**
     * Calculate total NET for group carts using cached carts.
     */
    public function getTotalNet(): float
    {
        // Use cached carts
        return $this->getCarts()->sum(function (FrontCart $cart) {
            return $cart->lines->sum('total_net');
        });
    }

    /**
     * Get the expiration time for group carts using cached carts.
     */
    public function getExpirationTime(): Carbon|null
    {
        // Use cached carts
        $carts = $this->getCarts();

        if ($carts->isEmpty()) {
            return null;
        }

        return $carts->first()->updated_at->addSeconds(FrontCartAccessor::ORDER_TTL_SECONDS);
    }

    /**
     * Clear all group carts and optionally replenish stock.
     */
    public function clearCart(bool $replenishStock = true): void
    {
        FrontCart::setReplenishStock($replenishStock);

        // Use cached carts
        $this->getCarts()->each(fn(FrontCart $cart) => $cart->delete());

        FrontCart::resetReplenishStock();
        $this->cachedCarts = null; // Clear cache after deleting carts
    }

    /**
     * Get all service lines from group carts using cached carts.
     */
    public function getServiceLines(): Collection
    {
        // Use cached carts
        return $this->getCarts()->flatMap(function (FrontCart $cart) {
            return $cart->lines()->where('shoppable_type', Sellable::class)->get();
        });
    }

    /**
     * Get all stay lines from group carts using cached carts.
     */
    public function getStayLines(): Collection
    {
        // Use cached carts
        return $this->getCarts()->flatMap(function (FrontCart $cart) {
            return $cart->lines()->where('shoppable_type', 'stay')->get();
        });
    }

    /**
     * Remove a service from a specific group cart.
     */
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

        if ( ! $serviceLine) {
            $error = "ServiceCart not found in cart";

            return false;
        }


        FrontCartActions::deleteCartLine($serviceLine);

        return true;
    }

    public function getPayableAmount(): int
    {
        return $this->getTotalTtc() - $this->getTotalPec();
    }
}
