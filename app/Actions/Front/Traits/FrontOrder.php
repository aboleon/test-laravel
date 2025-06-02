<?php

namespace App\Actions\Front\Traits;

use App\Accessors\Front\FrontCache;
use App\Accessors\Front\FrontCartAccessor;
use App\Accessors\OrderAccessor;
use App\Actions\Order\PecActionsFront;
use App\Enum\EventDepositStatus;
use App\Enum\OrderAmendedType;
use App\Enum\OrderClientType;
use App\Enum\OrderMarker;
use App\Enum\OrderOrigin;
use App\Enum\OrderType;
use App\Enum\PaymentMethod;
use App\Models\EventContact;
use App\Models\FrontCartLine;
use App\Models\FrontTransaction;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Order\Accompanying;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\Order\Cart\GrantDepositCart;
use App\Models\Order\Cart\SellableDepositCart;
use App\Models\Order\Cart\ServiceAttribution;
use App\Models\Order\Cart\ServiceCart;
use App\Models\Order\Cart\TaxRoomCart;
use App\Models\Order\EventDeposit;
use App\Models\Order\Invoiceable;
use App\Models\Order\RoomNote;
use App\Models\Payment;
use App\Models\PaymentCall;
use App\Models\User;
use App\Models\Vat;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MetaFramework\Accessors\VatAccessor;
use MetaFramework\Traits\Responses;
use Throwable;

trait FrontOrder
{
    private EventContact $eventContact;
    public FrontCartAccessor $cartAccessor;
    private Order $order;
    protected ?Order $depositOrder = null;
    protected ?PecActionsFront $pec;
    protected ?OrderAccessor $orderAccessor;

    use Responses;

    public function getCartAccessor(): FrontCartAccessor
    {
        return $this->cartAccessor;
    }

    private function dispatchOrderCarts(): void
    {
        Log::info('Store Order Carts');

        $this->attachServiceLinesToOrder($this->cartAccessor->getServiceLines(), $this->cart->transaction);
        $this->attachStayLinesToOrder($this->cartAccessor->getStayLines());
    }

    private function attachStayLinesToOrder(Collection $stayLines, bool $processAmendable = false): void
    {
        if ($stayLines->isEmpty()) {
            return;
        }

        [$cartNet, $cartVat, $cartPec] = [0, 0, 0];


        $metaLineInfo                       = $stayLines->first()->meta_info;
        $this->order->participation_type_id = $metaLineInfo['participation_type_id'];
        $this->order->save();

        foreach ($stayLines as $line) {
            $meta = $line->meta_info;
            $this->handleAccompanyingDetails($meta);
            $this->handleRoomNotes($meta);

            [$cartNet, $cartVat, $cartPec] = $this->processStayLine($line, $cartNet, $cartVat, $cartPec);
        }
    }


    private function handleAccompanyingDetails(array $meta): void
    {
        if ( ! empty($meta['accompanying_details'])) {
            Accompanying::query()->create([
                'order_id' => $this->order->id,
                'room_id'  => $meta['room_id'],
                'total'    => $meta['nb_person'] - 1,
                'names'    => $meta['accompanying_details'],
            ]);
        }
    }

    private function handleRoomNotes(array $meta): void
    {
        if ( ! empty($meta['comment'])) {
            $userId = $meta['event_contact_id']
                ? EventContact::find($meta['event_contact_id'])->user_id ?? null
                : null;

            RoomNote::query()->create([
                'order_id' => $this->order->id,
                'room_id'  => $meta['room_id'],
                'note'     => $meta['comment'],
                'user_id'  => $userId,
            ]);
        }
    }

    private function saveAccommodationCart(array $meta, string $date, float $priceTtc, float $net, float $vat, float $pec): void
    {
        AccommodationCart::create([
            'order_id'             => $this->order->id,
            'date'                 => $date,
            'event_hotel_id'       => $meta['accommodation_id'],
            'room_group_id'        => $meta['room_group_id'],
            'room_id'              => $meta['room_id'],
            'vat_id'               => $meta['vat_id'],
            'unit_price'           => $priceTtc,
            'quantity'             => 1,
            'total_net'            => $net,
            'total_vat'            => $vat,
            'total_pec'            => $pec,
            'accompanying_details' => $meta['accompanying_details'],
            'event_contact_id'     => $meta['event_contact_id'],
            'comment'              => $meta['comment'],
            'amended_cart_id'      => $meta['amendable'] == OrderAmendedType::CART->value
                ? $meta['amendable_id'] : null,
        ]);
    }

    private function updateOrderKeys(PaymentCall $paymentCall): void
    {
        Log::info('Update order ids');
        $paymentCall->update(['order_id' => $this->order->id]);
        if (!$paymentCall->isGroupManager()) {
            $paymentCall->cart->update(['order_id' => $this->order->id]);
        }

        if ($paymentCall->transaction) {
            $paymentCall->transaction->update(['order_id' => $this->order->id]);
        }
    }

    private function processStayLine(FrontCartLine $line, $cartNet, $cartVat, $cartPec): array
    {
        $meta = $line->meta_info;

        foreach ($meta['price_per_night'] as $date => $priceTtc) {
            [$net, $vat, $pec] = $this->calculateStayCosts($line, $priceTtc, $date);

            $this->saveAccommodationCart($meta, $date, $priceTtc, $net, $vat, $pec);

            $cartNet += $net;
            $cartVat += $vat;
            $cartPec += $pec;
        }

        if (empty($meta['amendable'])) {
            $this->handleProcessingFees($meta, $cartNet, $cartVat, $cartPec);
        }

        return [$cartNet, $cartVat, $cartPec];
    }


    private function calculateStayCosts(FrontCartLine $line, float $priceTtc, string $date): array
    {
        $meta     = $line->meta_info;
        $quantity = 1;
        $vatId    = $meta['vat_id'] ?? VatAccessor::defaultRate()['id'];

        $net = VatAccessor::netPriceFromVatPrice($priceTtc, $vatId) * $quantity;
        $vat = VatAccessor::vatForPrice($priceTtc, $vatId) * $quantity;

        $pec = $line->total_pec > 0 ? ($meta['pec_per_night'][$date] ?? 0) : 0;

        if ($pec > 0) {
            $remainingTtc = $priceTtc - $pec;
            if ($remainingTtc <= 0) {
                $net = 0;
                $vat = 0;
            } else {
                $net = VatAccessor::netPriceFromVatPrice($remainingTtc, $vatId);
                $vat = $remainingTtc - $net;
            }
        }

        return [$net, $vat, $pec];
    }

    private function addGrantWaiver(Collection $grantWaiverFeesLines, FrontTransaction $transaction)
    {
        $pecPaid = false;

        foreach ($grantWaiverFeesLines as $line) {
            /**
             * @var FrontCartLine $line
             */
            $tn    = $line->total_net;
            $tv    = $line->total_ttc - $tn;
            $meta  = $line->meta_info;
            $vatId = $meta['vat_id'] ?? $line->vat_id;


            $eventDeposit = EventDeposit::create([
                'order_id'         => $this->order->id,
                'event_id'         => $this->order->event_id,
                'shoppable_type'   => $line->shoppable_type,
                'shoppable_id'     => $line->shoppable_id,
                'vat_id'           => $vatId,
                'event_contact_id' => $this->cart->event_contact_id,
                'shoppable_label'  => "Caution pour le grant ".$meta['grant_title'],
                'total_net'        => $tn,
                'total_vat'        => $tv,
                'status'           => EventDepositStatus::PAID->value,
                'paybox_num_trans' => $transaction->num_trans, // TODO A supprimer
                'paybox_num_appel' => $transaction->num_appel, // TODO A supprimer
            ]);

            $pecPaid = true;


            $grantDepositCart = GrantDepositCart::create([
                'order_id'         => $this->order->id,
                'event_grant_id'   => $meta['grant_id'],
                'event_deposit_id' => $eventDeposit->id,
                'vat_id'           => $vatId,
                'unit_price'       => $line->unit_ttc,
                'quantity'         => 1,
                'total_net'        => $tn,
                'total_vat'        => $tv,
                'event_contact_id' => $this->cart->event_contact_id,
            ]);

            $this->order->total_net += $tn;
            $this->order->total_vat += $tv;
            $this->order->marker    = OrderMarker::GHOST->value;
            $this->order->save();
        }

        if ($pecPaid) {
            $this->eventContact->update(['pec_enabled' => 1]);
        }
    }

    public function getOrder(): \App\Models\Order
    {
        return $this->order;
    }

    public function attachInvoice(): void
    {
        Log::info('Attach Invoice');

        try {
            $client_id = $this->order->origin == OrderOrigin::FRONT->value ? FrontCache::getEventContact()->user_id : $this->order->client_id;
        } catch (Throwable) {
            $client_id = User::first()->id;
        }

        Invoice::firstOrcreate([
            'order_id' => $this->order->id,
        ], [
            'created_by' => $client_id,
        ]);
    }

    private function handleProcessingFees(array $meta, &$cartNet, &$cartVat, &$cartPec): void
    {
        if ( ! empty($meta['processing_fee_ttc'])) {
            $pecAccommodation = $this->cart->pec_eligible
                ? collect($this->pec->getPecDistributionResult()->getAccommodation()['items']['taxroom'] ?? [])
                    ->firstWhere('room_id', $meta['room_id'])
                : null;

            $hasPec = ! is_null($pecAccommodation);

            $processingFeeNet = $meta['processing_fee_ttc'] - $meta['processing_fee_vat'];

            $taxRoom = new TaxRoomCart();
            $taxRoom->fill([
                'order_id'         => $this->order->id,
                'event_hotel_id'   => $meta['accommodation_id'],
                'room_id'          => $meta['room_id'],
                'amount'           => $hasPec ? 0 : $meta['processing_fee_ttc'],
                'amount_net'       => $hasPec ? 0 : $processingFeeNet,
                'amount_vat'       => $hasPec ? 0 : $meta['processing_fee_vat'],
                'amount_pec'       => $hasPec ? $pecAccommodation['unit_price'] : 0,
                'vat_id'           => $meta['processing_fee_vat_id'],
                'event_contact_id' => $meta['event_contact_id'],
            ])->save();

            if ( ! $hasPec) {
                $cartNet += $processingFeeNet;
                $cartVat += $meta['processing_fee_vat'];
            } else {
                $cartPec += $meta['processing_fee_ttc'];
            }
        }
    }


    /**
     * @throws Exception
     */
    private function setInvoiceable(Order $order): void
    {
        Log::info('Store Invoiceable');
        $this->orderAccessor = (new OrderAccessor($order));

        if ($this->orderAccessor->hasValidBillingAddress()) {
            $billingAddress = $this->orderAccessor->getBillingAddress();
            $account        = $this->orderAccessor->account();

            $data = [
                'account_type'  => $order->client_type,
                'account_id'    => $order->client_id,
                'address_id'    => $billingAddress->id,
                'order_id'      => $order->id,
                'company'       => $account['company'],
                'vat_number'    => $billingAddress->vat_number ?? null,
                'first_name'    => $account['first_name'],
                'last_name'     => $account['last_name'],
                'postal_code'   => $billingAddress->postal_code,
                'locality'      => $billingAddress->locality,
                'cedex'         => $billingAddress->cedex,
                'country_code'  => $billingAddress->country_code ?? 'FR',
                'text_address'  => $billingAddress->text_address,
                'department'    => $billingAddress->department ?? null,
                'street_number' => $billingAddress->street_number,
                'route'         => $billingAddress->route,
                'complementary' => $billingAddress->complementary,
            ];
            try {
                $invoiceable = new Invoiceable($data);
                $order->invoiceable()->save($invoiceable);
            } catch (Throwable $e) {
                $this->responseException($e);
            }
        }
    }

    private function setPayment(PaymentCall $paymentCall): void
    {
        Log::info('Store Payment');
        $total = $paymentCall->total;

        if ($this->depositOrder) {
            $total -= $this->depositOrder->total_net + $this->depositOrder->total_vat;
        }

        if ($total > 0) { // Ne pas enregistrer un paiement à zéro
            $this->order->payments()->save(
                (new Payment([
                    'order_id'           => $this->order->id,
                    'date'               => now(),
                    'payment_method'     => PaymentMethod::CB_PAYBOX->value,
                    'transaction_id'     => $paymentCall->transaction?->id,
                    'amount'             => $total,
                    'transaction_origin' => OrderOrigin::FRONT->value,
                ])),
            );
        }
    }

    public function getDepositOrder(): ?Order
    {
        return $this->depositOrder;
    }


    # ----------------------------------------


    private function attachServiceLinesToOrder(Collection $serviceLines, ?FrontTransaction $transaction = null): void
    {
        if ($serviceLines->isEmpty()) {
            return;
        }

        foreach ($serviceLines as $line) {
            $this->processServiceLine($line, $transaction);
        }
    }

    private function processServiceLine(FrontCartLine $line, ?FrontTransaction $transaction = null): void
    {
        $quantity     = $line->quantity;
        $totalPec     = $line->total_pec;
        $totalNet     = $line->total_net;
        $meta         = $line->meta_info;
        $depositTtc   = $meta['deposit_ttc'] ?? null;
        $unitTtc      = $line->unit_ttc;
        $remainingTtc = $line->total_ttc - $totalPec;
        $isPec        = $remainingTtc <= 0;

        if ($isPec) {
            $totalNet = 0;
        }

        $totalVat = $remainingTtc - $totalNet;

        if ($depositTtc) {
            $this->processDeposit($line, $meta, $transaction, $remainingTtc, $isPec);
            [$totalNet, $totalVat] = $this->recomputeSellableValues($remainingTtc, $depositTtc, $line->vat_id);
            $unitTtc -= $depositTtc;
        }

        $this->createServiceCart($line, $unitTtc, $quantity, $totalNet, $totalVat, $totalPec);

        if (OrderClientType::GROUP->value === $this->order->client_type) {
            $this->createServiceAttribution($line, $quantity);
        }
    }

    private function processDeposit(FrontCartLine $line, array $meta, ?FrontTransaction $transaction, float &$remainingTtc, bool $isPec): void
    {
        $depositNet = $meta['deposit_net'];
        $depositVat = $meta['deposit_ttc'] - $depositNet;

        $this->order->total_net -= $meta['deposit_net'];
        $this->order->total_vat -= $depositVat;
        $this->order->save();

        if ($isPec) {
            $depositNet = 0;
            $depositVat = 0;
        }

        $this->createDepositOrder($depositNet, $depositVat);
        $deposit = $this->createEventDeposit($line, $meta, $transaction, $depositNet, $depositVat);
        $this->createSellableDepositCart($deposit, $meta);

        $remainingTtc -= $meta['deposit_ttc'];
    }

    private function createDepositOrder(float $depositNet, float $depositVat): void
    {
        $this->depositOrder            = $this->order->replicate();
        $this->depositOrder->uuid      = Str::uuid();
        $this->depositOrder->total_net = $depositNet;
        $this->depositOrder->total_vat = $depositVat;
        $this->depositOrder->total_pec = 0;
        $this->depositOrder->marker    = OrderMarker::GHOST->value;
        $this->depositOrder->type      = OrderType::DEPOSIT->value;
        $this->depositOrder->save();

        $this->setInvoiceable($this->depositOrder);

        // Save prorata payment for deposit
        $this->depositOrder->payments()->save(
            (new Payment([
                'order_id'           => $this->depositOrder->id,
                'date'               => now(),
                'payment_method'     => PaymentMethod::CB_PAYBOX->value,
                'transaction_id'     => $this->paymentCall->transaction?->id,
                'amount'             => $depositNet + $depositVat,
                'transaction_origin' => OrderOrigin::FRONT->value,
            ])),
        );
    }

    private function createEventDeposit(FrontCartLine $line, array $meta, ?FrontTransaction $transaction, float $depositNet, float $depositVat): EventDeposit
    {
        $deposit                   = new EventDeposit();
        $deposit->order_id         = $this->depositOrder->id;
        $deposit->event_id         = $this->depositOrder->event_id;
        $deposit->shoppable_type   = $line->shoppable_type;
        $deposit->shoppable_id     = $line->shoppable_id;
        $deposit->shoppable_label  = $line->shoppable->title;
        $deposit->vat_id           = $meta['deposit_vat_id'];
        $deposit->event_contact_id = $this->cart->event_contact_id;
        $deposit->total_net        = $depositNet;
        $deposit->total_vat        = $depositVat;
        $deposit->status           = EventDepositStatus::PAID->value;

        if ($transaction) {
            $deposit->paybox_num_trans = $transaction->num_trans;
            $deposit->paybox_num_appel = $transaction->num_appel;
        }

        $deposit->save();

        return $deposit;
    }

    private function createSellableDepositCart(EventDeposit $deposit, array $meta): void
    {
        SellableDepositCart::create([
            'order_id'         => $this->order->id,
            'event_deposit_id' => $deposit->id,
            'vat_id'           => $meta['deposit_vat_id'],
            'unit_price'       => $meta['deposit_ttc'],
            'quantity'         => 1,
            'total_net'        => $deposit->total_net,
            'total_vat'        => $deposit->total_vat,
            'event_contact_id' => $this->cart->event_contact_id,
        ]);
    }

    private function recomputeSellableValues(float $remainingTtc, float $depositTtc, int $vatId): array
    {
        if ($remainingTtc <= 0) {
            return [0, 0];
        }

        $remainingTtc -= $depositTtc;
        if ($remainingTtc <= 0) {
            return [0, 0];
        }

        $sellableVat = Vat::find($vatId);
        $vatRate     = $sellableVat->rate / 100 / 100;
        $totalNet    = round($remainingTtc / (1 + $vatRate), 2);
        $totalVat    = $remainingTtc - $totalNet;

        return [$totalNet, $totalVat];
    }

    private function createServiceCart(FrontCartLine $line, float $unitTtc, int $quantity, float $totalNet, float $totalVat, float $totalPec): void
    {
        $cart             = new ServiceCart();
        $cart->order_id   = $this->order->id;
        $cart->service_id = $line->shoppable_id;
        $cart->vat_id     = $line->vat_id;
        $cart->unit_price = $unitTtc;
        $cart->quantity   = $quantity;
        $cart->total_net  = $totalNet;
        $cart->total_vat  = $totalVat;
        $cart->total_pec  = $totalPec;
        $cart->save();
    }

    private function createServiceAttribution(FrontCartLine $line, int $quantity): void
    {
        ServiceAttribution::create([
            'order_id'         => $this->order->id,
            'event_contact_id' => $this->cart->event_contact_id,
            'service_id'       => $line->shoppable_id,
            'quantity'         => $quantity,
            'assigned_by'      => $this->order->created_by,
        ]);
    }


}
