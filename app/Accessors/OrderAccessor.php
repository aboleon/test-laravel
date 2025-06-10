<?php

namespace App\Accessors;

use App\Enum\OrderAmendedType;
use App\Enum\OrderCartType;
use App\Enum\OrderClientType;
use App\Enum\OrderStatus;
use App\Models\Account;
use App\Models\EventManager\EventGroup;
use App\Models\EventManager\Sellable;
use App\Models\Order;
use App\Services\Validators\AddressValidator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MetaFramework\Interfaces\GooglePlacesInterface;
use MetaFramework\Traits\Responses;
use Throwable;

/**
 * @property Order $order
 */
class OrderAccessor
{
    use Responses;

    private ?EloquentCollection $serviceCart = null;
    private ?EloquentCollection $accommodationCart = null;
    private ?EloquentCollection $taxRoomCart = null;
    private ?EloquentCollection $grantDepositCart = null;
    private ?EloquentCollection $sellableDepositCart = null;

    private ?bool $isEmpty = null;

    public function __construct(public readonly Order $order) {}

    public function isOrder(): bool
    {
        return (bool)$this->order?->id;
    }

    public function isUnderCreation(): bool
    {
        return ! $this->isOrder();
    }

    public function serviceCart(): ?EloquentCollection
    {
        if ($this->serviceCart !== null) {
            return $this->serviceCart;
        }
        $this->serviceCart = $this->order->services;

        return $this->serviceCart;
    }

    public function serviceAttributions(): EloquentCollection|Collection
    {
        return $this->order->serviceAttribution ?? collect();
    }

    public function accommodationAttributions(): array
    {
        if ( ! $this->isOrder() or ! $this->isGroup()) {
            return [];
        }

        return DB::select(
            "
        SELECT
    a.date,
    a.event_hotel_id,
    a.room_group_id,
    a.room_id,
    b.name AS room_category,
    c.name AS room,
    h.name AS hotel_name,
    h.stars,
    ta.text_address AS hotel_address,
    d.capacity,
    oat.event_contact_id,
    SUM(a.quantity - IFNULL(a.cancelled_qty, 0)) AS total_quantity,
    COALESCE(
        (
            SELECT SUM(oa.quantity)
            FROM order_attributions oa
            WHERE oa.order_id = a.order_id
            AND oa.shoppable_type = '".OrderCartType::ACCOMMODATION->value."'
            AND oa.shoppable_id = a.room_id
            AND JSON_UNQUOTE(JSON_EXTRACT(oa.configs, '$.date')) = a.date
        ),
        0
    ) AS attributed
FROM order_cart_accommodation a
JOIN event_accommodation_room_groups b ON a.room_group_id = b.id
JOIN event_accommodation e ON a.event_hotel_id = e.id
JOIN hotels h ON e.hotel_id = h.id
JOIN event_accommodation_room d ON a.room_id = d.id
JOIN dictionnary_entries c ON d.room_id = c.id
LEFT JOIN order_attributions oat ON a.order_id = oat.order_id
LEFT JOIN hotel_address ta ON h.id = ta.id
WHERE a.order_id = ".$this->order->id."
GROUP BY
    a.date,
    a.event_hotel_id,
    a.room_group_id,
    a.room_id,
    h.name
ORDER BY h.name;

        ",
        );
    }

    public function accommodationCart(): ?EloquentCollection
    {
        if ($this->accommodationCart !== null) {
            return $this->accommodationCart;
        }
        $this->accommodationCart = $this->order->accommodation->load('wasAmended');

        return $this->accommodationCart;
    }

    public function taxRoomCart(): ?EloquentCollection
    {
        if ($this->taxRoomCart !== null) {
            return $this->taxRoomCart;
        }
        $this->taxRoomCart = $this->order->taxroom;

        return $this->taxRoomCart;
    }

    public function grantDepositCart(): ?EloquentCollection
    {
        if ($this->grantDepositCart !== null) {
            return $this->grantDepositCart;
        }
        $this->grantDepositCart = $this->order->grantDeposit;

        return $this->grantDepositCart;
    }

    public function sellableDepositCart(): ?EloquentCollection
    {
        if ($this->sellableDepositCart !== null) {
            return $this->sellableDepositCart;
        }
        $this->sellableDepositCart = $this->order->deposits->where('shoppable_type', Sellable::class);

        return $this->sellableDepositCart;
    }

    public function accommodationCartTotals(): array
    {
        return $this->computeTotals($this->accommodationCart());
    }

    public function taxRoomCartTotals(): array
    {
        if ( ! $this->taxRoomCart()) {
            return $this->defaultTotals();
        }

        return [
            'total_net' => round($this->taxRoomCart->pluck('amount_net')->flatten()->sum(), 2),
            'total_vat' => round($this->taxRoomCart->pluck('amount_vat')->flatten()->sum(), 2),
            'total_pec' => round($this->taxRoomCart->pluck('amount_pec')->flatten()->sum(), 2),
        ];
    }

    public function grantDepositCartTotals(): array
    {
        if ( ! $this->grantDepositCart()) {
            return $this->defaultTotals();
        }

        return [
            'total_net' => round($this->grantDepositCart->pluck('total_net')->flatten()->sum(), 2),
            'total_vat' => round($this->grantDepositCart->pluck('total_vat')->flatten()->sum(), 2),
            'total_pec' => 0,
        ];
    }

    public function sellableDepositCartTotals(): array
    {
        if ( ! $this->sellableDepositCart()) {
            return $this->defaultTotals();
        }

        return [
            'total_net' => round($this->sellableDepositCart->pluck('total_net')->flatten()->sum(), 2),
            'total_vat' => round($this->sellableDepositCart->pluck('total_vat')->flatten()->sum(), 2),
            'total_pec' => 0,
        ];
    }

    private function defaultTotals(): array
    {
        return [
            'total_net' => 0,
            'total_vat' => 0,
            'total_pec' => 0,
        ];
    }

    public function serviceCartTotals(): array
    {
        return $this->computeTotals($this->serviceCart());
    }

    public function computeTotals(?EloquentCollection $cart): array
    {
        if ( ! $cart) {
            return $this->defaultTotals();
        }

        return [
            'total_net' => round($cart->pluck('total_net')->flatten()->sum(), 2),
            'total_vat' => round($cart->pluck('total_vat')->flatten()->sum(), 2),
            'total_pec' => round($cart->pluck('total_pec')->flatten()->sum(), 2),
        ];
    }

    public function getOrderTotals(): array
    {
        return [
            'net' => $this->order->total_net,
            'vat' => $this->order->total_vat,
            'pec' => $this->order->total_pec,
        ];
    }

    public function getTotalsByCart(): array
    {
        return [
            'service'         => $this->serviceCartTotals(),
            'accommodation'   => $this->accommodationCartTotals(),
            'taxroom'         => $this->taxRoomCartTotals(),
            'grantDeposit'    => $this->grantDepositCartTotals(),
            'sellableDeposit' => $this->sellableDepositCartTotals(),
        ];
    }

    public function computeOrderTotalsFromCarts(): array
    {
        $total = $this->getTotalsByCart();

        return [
            'net' => round(array_sum(array_column($total, 'total_net')), 2),
            'vat' => round(array_sum(array_column($total, 'total_vat')), 2),
            'pec' => round(array_sum(array_column($total, 'total_pec')), 2),
        ];
    }

    public function netSubtotalsByVat(): array
    {
        return $this->subtotals('total_net');
    }

    private function subtotals(string $subtotal)
    {
        /*
           return array_sum(array_column($this->getTotalsByCart(), 'total_vat'));
           de($totals);
           */
        $collection = collect();

        if ($this->serviceCart()) {
            $collection = $collection->merge($this->serviceCart()->map(function ($item) use ($subtotal) {
                return collect($item)->only([$subtotal, 'vat_id', 'id']);
            }));
        }

        if ($this->accommodationCart()) {
            $collection = $collection->merge($this->accommodationCart()->map(function ($item) use ($subtotal) {
                return collect($item)->only([$subtotal, 'vat_id', 'id']);
            }));
        }

        if ($this->taxRoomCart()) {
            $subtotal_tax = str_replace('total', 'amount', $subtotal);
            $collection = $collection->merge(
                $this->taxRoomCart()->map(function ($item) use ($subtotal, $subtotal_tax) {
                    return collect($item)->only([$subtotal_tax, 'vat_id', 'id']);
                })->map(function ($item) use ($subtotal, $subtotal_tax) {
                    $item[$subtotal] = $item[$subtotal_tax];
                    unset($item[$subtotal_tax]);

                    return $item;
                }),
            );
        }


        return $collection->groupBy('vat_id')->map(function ($items) use ($subtotal) {
            return $items->reduce(function ($carry, $item) use ($subtotal) {
                return $carry + $item[$subtotal];
            }, 0);
        })->toArray();
    }

    public function vatSubtotalsByVat(): array
    {
        return $this->subtotals('total_vat');
    }

    public function hasItems(): bool
    {
        return $this->serviceCart() || $this->accommodationCart();
    }

    public function getTotal(): int|float
    {
        $totals = $this->computeOrderTotalsFromCarts();

        return $totals['net'] + $totals['vat'];
    }

    public function attributedTo(): array
    {
        switch ($this->order->client_type) {
            case 'group':
                $group = new GroupAccessor($this->order->group);
                $data  = [
                    'name'         => $this->order->group->name,
                    'company'      => $this->order->group->company,
                    'address'      => str_replace(',', '<br>', $group->billingAddress()?->text_address),
                    'country_name' => \MetaFramework\Accessors\Countries::getCountryNameByCode(
                        $group->billingAddress()?->country_code,
                    ),
                ];
                break;
            default:
                $account = new Accounts($this->order->account);
                $data    = [
                    'company'      => $account->companyName(''),
                    'name'         => $this->order->account->names(),
                    'address'      => str_replace(',', '<br>', $account->billingAddress()?->text_address),
                    'country_name' => \MetaFramework\Accessors\Countries::getCountryNameByCode(
                        $account->billingAddress()?->country_code,
                    ),
                    'lg'           => $account->getLocale(),
                ];
        }
        $data['type'] = $this->order->client_type;
        $data['type_translated'] = OrderClientType::translated($this->order->client_type);

        return $data;
    }

    public function getBillingAddress(): ?GooglePlacesInterface
    {
        try {
            if ($this->isGroup()) {
                return (new GroupAccessor($this->order->group))->billingAddress();
            }

            return (new Accounts($this->order->account))->billingAddress();
        } catch (Throwable $exception) {
            $this->responseException($exception);

            return null;
        }
    }

    public function hasValidBillingAddress(): bool
    {
        return (new AddressValidator($this->getBillingAddress()))->isValid();
    }

    public function account(): array
    {
        $baseData = $this->attributedTo();
        switch ($this->order->client_type) {
            case 'group':
                $groupAccessor = (new GroupAccessor($this->order->group))->setEvent($this->order->event);
                $eventContact  = $groupAccessor->getMainContactForEvent();

                $account = $eventContact ? new Accounts($eventContact->account) : null;

                $data = [
                    'lg'         => $eventContact && $account ? $account->getLocale() : config('app.fallback_locale'),
                    'first_name' => ucfirst(mb_strtolower($baseData['name'])),
                    'last_name'  => $baseData['company'],
                    'email'      => $eventContact?->account->email,
                ];
                break;
            default:
                $account = $this->order->account;

                $data = [
                    'id' => $account->id,
                    'first_name' => ucfirst(mb_strtolower($account->first_name)),
                    'last_name'  => $account->last_name,
                    'email'      => $account->email,
                ];
        }

        $accountData = array_merge($baseData, $data);
        ksort($accountData);
        return $accountData;
    }

    public function isSamePayer(): bool
    {
        return ($this->order->invoiceable?->account_type == $this->order->client_type) && ($this->order->invoiceable?->account_id == $this->order->client_id);
    }

    public function invoiceableAddress(): array
    {
        if ( ! $this->order->invoiceable) {
            return [];
        }

        return array_filter([
            'name'       => $this->order->invoiceable->account->names(),
            'department' => $this->order->invoiceable->department,
            'address'    => str_replace(',', '<br>', $this->order->invoiceable->text_address),
        ]);
    }

    public function invoiceableMail(): string
    {
        if ( ! $this->order->invoiceable) {
            return config('app.default_mail');
        }

        $user_id = match ($this->order->invoiceable->account_type) {
            'group' => EventGroup::firstWhere([
                'event_id' => $this->order->event_id,
                'group_id' => $this->order->invoiceable->account_id,
            ])->whereNotNull('main_contact_id')
                ->value('main_contact_id'),
            'contact' => $this->order->invoiceable->account_id,
        };

        return Account::where('id', $user_id)->value('email') ?: config('app.default_mail');
    }

    public function totalPayable(): int
    {
        return ($this->order->total_net + $this->order->total_vat) - $this->order->total_pec;
    }

    /**
     * Calcule ce qui reste à payer sur une collection de commandes
     *
     * @param  EloquentCollection|Collection  $orders
     *
     * @return array
     */
    public static function calculateRemainingAmounts(EloquentCollection|Collection $orders): array
    {
        return $orders->mapWithKeys(function ($order) {
            $totalAmount     = $order->total_net + $order->total_vat;
            $totalPayments   = $order->payments->sum('amount');
            $remainingAmount = $totalAmount - $totalPayments;

            return [$order->id => $remainingAmount];
        })->toArray();
    }

    public function calculateRemainingAmountToPay(): int|float
    {
        $totalAmount = $this->order->total_net + $this->order->total_vat;

        return $totalAmount > 0
            ? $totalAmount - $this->order->payments->sum('amount')
            : 0;
    }

    public function isFullyPaid(): bool
    {
        return $this->calculateRemainingAmountToPay() == 0;
    }


    public function isInvoiced(): bool
    {
        return $this->isSuborder() or (bool)$this->order->invoice();
    }

    public function isFrontGroupOrder(): bool
    {
        return $this->order->suborders()->exists();
    }

    public function isNotFrontGroupOrder(): bool
    {
        return ! $this->order->suborders()->exists();
    }

    public function isSuborder(): bool
    {
        return ! is_null($this->order->parentOrder);
    }

    public function parentOrder(): ?Order
    {
        return $this->order->parentOrder;
    }

    public function invoiceable(): ?Order\Invoiceable
    {
        if ($this->isSuborder()) {
            return $this->parentOrder()->invoiceable;
        }

        return $this->order->invoiceable;
    }

    public function wasAmendedByAnotherOrder(): bool
    {
        return ! is_null($this->order->amended_by_order_id) && $this->order->amend_type == OrderAmendedType::ORDER->value;
    }

    public function hasAmendedAnotherOrder()
    {
        return $this->order->amended_order_id && $this->order->amend_type == OrderAmendedType::ORDER->value;
    }

    public function getAmendedOrder(): ?Order
    {
        return $this->order->amendedOrder;
    }

    public function isNotGroup(): bool
    {
        return ! $this->isGroup();
    }

    public function isGroup(): bool
    {
        return $this->order->client_type == OrderClientType::GROUP->value;
    }

    public function isMadeByAdmin(): bool
    {
        return $this->order->origin == 'back';
    }

    public function isOrator(): bool
    {
        return $this->order->client_type == OrderClientType::ORATOR->value or ( ! $this->order->id && request()->has('as_orator'));
    }

    public function isRegular(): bool
    {
        return $this->order->client_type == OrderClientType::CONTACT->value;
    }

    public function isPaid(): bool
    {
        return $this->order->status == OrderStatus::PAID->value;
    }

    public function isUnpaid(): bool
    {
        return !$this->isPaid();
    }

    public function hasAccommodationQuota(): Collection
    {
        return $this->accommodationCart()->filter(fn($item) => $item->on_quota == 1);
    }

    public function hasPartialCancellation(): Collection
    {
        return $this->accommodationCart()->filter(fn($item) => ! is_null($item->cancelled_at));
    }

    public function hasEmptyContent(): bool
    {
        if (is_null($this->isEmpty)) {
            $this->isEmpty = $this->order->hasEmptyContent();
        }

        return $this->isEmpty ?? false;
    }

    public function serviceCartLeft(): Collection
    {
        return $this->serviceCart()->filter(fn($service) => is_null($service->cancelled_at));
    }

    public function accommodationCartLeft(): Collection
    {
        return $this->accommodationCart()
            ->filter(fn($accommodation) => is_null($accommodation->cancelled_at) || ($accommodation->cancelled_qty !== $accommodation->quantity))
            ->reject(fn($accommodation) => $accommodation->wasAmended)
            ->map(function ($accommodation) {
                $cancelledQuantity = $accommodation->getCancellations()->sum('quantity');
                $accommodation->quantity -= $cancelledQuantity;
                return $accommodation;
            });
    }
}
