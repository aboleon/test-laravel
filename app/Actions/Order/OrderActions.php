<?php

namespace App\Actions\Order;

use App\Accessors\EventAccessor;
use App\Accessors\EventContactAccessor;
use App\Accessors\OrderAccessor;
use App\Enum\CancellationStatus;
use App\Enum\OrderCartType;
use App\Events\OrderRowCancel;
use App\Mail\Front\Order\DeclineVenueMail;
use App\Mail\Front\Order\OrderAccommodationCancellationRequestMail;
use App\Mail\Front\Order\OrderCancellationRequestMail;
use App\Mail\Front\Order\OrderServiceCancellationRequestMail;
use App\Models\Account;
use App\Models\Event;
use App\Models\Order;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\Order\Cart\AccommodationCartCancellation;
use App\Models\Order\Cart\ServiceCart;
use App\Traits\Models\AccountModelTrait;
use App\Traits\Models\EventContactModelTrait;
use App\Traits\Models\EventModelTrait;
use App\Traits\Models\OrderModelTrait;
use Exception;
use Illuminate\Support\Facades\Mail;
use MetaFramework\Traits\Ajax;
use MetaFramework\Traits\Responses;
use Throwable;

class OrderActions
{
    use Ajax;
    use Responses;
    use EventModelTrait;
    use EventContactModelTrait;
    use OrderModelTrait;
    use AccountModelTrait;

    protected string $adminSubscriptions;
    protected string $cartType = '';
    protected null|ServiceCart|AccommodationCart $cart = null;

    public function updateTotals(array $totals): void
    {
        $this->order->total_net = $totals['net'];
        $this->order->total_vat = $totals['vat'];
        $this->order->total_pec = $totals['pec'];
        $this->order->save();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function cancelCartItem(): array
    {
        $comes_from_front = request()->filled('origin') && request('origin') == 'front';

        $cancelled_qty = (int)request('qty');
        try {
            // Checks
            $this->setEvent((int)request('event_id'))->validateModelProperty('event', __('errors.event_not_found'));

            if ($comes_from_front) {
                $this->setAccount(auth()->id())->validateModelProperty('account', __('errors.account_not_found'));
            }

            $this->cartType = (string)request('type');
            if ( ! in_array($this->cartType, OrderCartType::keys())) {
                throw new Exception(__('errors.cart_type_uknown', ['type' => $this->cartType]));
            }

            $cart_id = (int)request('cart_id');
            if ( ! $cart_id) {
                throw new Exception(__('errors.cart_id_missing'));
            }

            $this->cart = match ($this->cartType) {
                OrderCartType::SERVICE->value => ServiceCart::query()->find($cart_id),
                OrderCartType::ACCOMMODATION->value => AccommodationCart::query()->find($cart_id),
                default => throw new Exception(__('errors.cart_type_uknown', ['type' => $this->cartType])),
            };

            if ( ! $this->cart) {
                throw new Exception(__('errors.cart_not_found'));
            }

            $this->order = $this->cart->order;
            $this->checkOrder($this->cart->order->id);
            $orderAccessor = (new OrderAccessor($this->cart->order));

            if ($comes_from_front) {
                $this->checkOrderBelongsToClient();
                $this->checkOrderBelongsToEvent();
            }

            // Alter cart state
            $this->cart->cancellation_request = now();

            // Direct cancelling in BO
            if ( ! $comes_from_front) {
                if ($this->cart instanceof AccommodationCart) {
                    $this->cart->cancelled_qty += $cancelled_qty;
                    if ($this->cart->isFullyCancelled()) {
                        $this->cart->cancelled_at = now();
                        $this->responseElement('cancelled_at', $this->cart->cancelled_at->format('d/m/Y à H:i'));
                    }
                }

                $this->cart->cancellations()->save(
                    (new AccommodationCartCancellation([
                        'quantity'     => $cancelled_qty,
                        'cancelled_at' => now(),
                    ])),
                );

                $this->responseElement('cancelled_qty', $cancelled_qty);
                $this->responseSuccess('Vous avez annulé '.$cancelled_qty.' '.strtolower(trans_choice('front/accommodation.room', $cancelled_qty)));

                event(new OrderRowCancel($this->cart->order, $this->cart));
            }

            $this->cart->save();
            $this->responseElement('cart_qty', $this->cart->quantity);
            $this->responseElement('cart_cancelled_qty', $cancelled_qty);
            $this->responseElement('new_qty', $this->cart->computedQuantity());
            $this->responseElement('remaining', (int)request('remaining') - $cancelled_qty);
            $this->responseElement('is_group', (int)$orderAccessor->isOrder());
            $this->responseElement('cancelled_at', now()->format('d/m/Y H:i'));

            if ($comes_from_front) {
                // Send mail
                $this->sentCartCancellationNotification();
            } else {
                $this->manageCancellationType($this->cart->order);
            }
        } catch (Throwable $e) {
            $this->responseError($e->getMessage());
        }

        return $this->fetchResponse();
    }

    /**
     * @throws Exception
     */
    public function declineVenue(): array
    {
        // Checks
        $this->checkEventUser((int)request('event_id'), auth()->id());

        if ($this->hasErrors()) {
            return $this->fetchResponse();
        }

        // Send the mail
        $this->getAdminSubscriptionEmail();

        // TODO change ordercancellation to timestamp
        $eventContact                     = EventContactAccessor::getEventContactByEventAndUser($this->event, $this->account);
        $eventContact->order_cancellation = 1;
        $eventContact->save();

        // Repercuter la demande dans toutes les commandes utilisateurs
        Order::whereKey($eventContact->orders()->where('event_id', $eventContact->event_id)->pluck('id'))
            ->update(['cancellation_request' => now()]);


        try {
            Mail::to($this->adminSubscriptions)->send(
                new DeclineVenueMail(
                    $this->event,
                    $this->account,
                ),
            );
            $this->responseSuccess(__('ui.demand_taken_in_account'));
        } catch (Throwable) {
            $this->responseError(__('errors.mail_not_sent', ['email' => $this->adminSubscriptions]));
        }


        return $this->fetchResponse();
    }

    /**
     * @throws Exception
     */
    public function checkOrder(int $order_id): self
    {
        if ($this->order instanceof Order) {
            return $this;
        }

        $this
            ->setOrder($order_id)
            ->validateModelProperty('order', __('errors.order_not_found'));

        return $this;
    }

    public function cancelOrder(): array
    {
        try {
            // Checks
            $this->checkEventUser((int)request('event_id'), auth()->id());
            $this->checkOrder((int)request('order_id'));
            $this->checkOrderBelongsToClient();
            $this->checkOrderBelongsToEvent();
            /*
            if ($this->order->hasAnyPayments()) {
                throw new Exception(__('errors.order_cannot_be_cancelled_because_of_payments') . ' ' . __('ui.contact_support'));
            }
            */
            // Alter the order state
            $this->order->cancellation_request = now();
            $this->order->save();

            $this->order->services()->update([
                'cancellation_request' => now(),
            ]);
            $this->order->accommodation()->update([
                'cancellation_request' => now(),
            ]);

            // Send the mail
            $this->getAdminSubscriptionEmail();

            try {
                Mail::to($this->adminSubscriptions)->send(
                    new OrderCancellationRequestMail(
                        $this->event,
                        $this->order,
                        $this->account,
                    ),
                );
                $this->responseSuccess(__('ui.demand_taken_in_account'));
            } catch (Throwable) {
                $this->responseError(__('errors.mail_not_sent', ['email' => $this->adminSubscriptions]));
            }
        } catch (Throwable $e) {
            $this->responseError($e->getMessage());
        }

        return $this->fetchResponse();
    }

    /**
     * @throws Exception
     */
    private function checkEventUser(int $event_id, int $account_id): self
    {
        if ( ! $this->event instanceof Event) {
            $this->setEvent($event_id)->validateModelProperty('event', __('errors.event_not_found'));
        }
        if ( ! $this->account instanceof Account) {
            $this->setAccount($account_id)->validateModelProperty('account', __('errors.account_not_found'));
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    private function checkOrderBelongsToClient(): self
    {
        if ($this->order->client_id !== $this->account->id) {
            $this->responseError(__('errors.not_authorised_to_cancel'));
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    private function checkOrderBelongsToEvent(): self
    {
        if ($this->order->event_id != $this->event->id) {
            $this->responseError(__('errors.not_linked_to_event_cancellation'));
        }

        return $this;
    }

    private function getAdminSubscriptionEmail(): void
    {
        $this->adminSubscriptions = EventAccessor::getAdminSubscriptionEmail($this->event);
    }

    private function sentCartCancellationNotification(): void
    {
        $this->getAdminSubscriptionEmail();

        try {
            switch ($this->cartType) {
                case OrderCartType::ACCOMMODATION->value :
                    Mail::to($this->adminSubscriptions)->send(
                        new OrderAccommodationCancellationRequestMail(
                            $this->event,
                            $this->order,
                            $this->cart,
                            $this->account,
                        ),
                    );
                    break;
                default:
                    Mail::to($this->adminSubscriptions)->send(
                        new OrderServiceCancellationRequestMail(
                            $this->event,
                            $this->order,
                            $this->cart,
                            $this->account,
                        ),
                    );
            }

            $this->responseSuccess(__('ui.demand_taken_in_account'));
        } catch (Throwable) {
            $this->responseError(__('errors.mail_not_sent', ['email' => $this->adminSubscriptions]));
        }
    }

    public function manageCancellationType(Order $order): void
    {
        $order->load(['accommodation', 'services']);


        $cancellationType = CancellationStatus::default();

        $allAccommodationsCancelled = $order->accommodation->every(
            fn($acc) => $acc->quantity === $acc->cancelled_qty,
        );
        $allServicesCancelled       = $order->services->every(
            fn($srv) => $srv->quantity === $srv->cancelled_qty,
        );


        if ($allAccommodationsCancelled && $allServicesCancelled) {
            $cancellationType = CancellationStatus::FULL->value;
        }

        $order->cancellation_status = $cancellationType;

        if ($cancellationType === CancellationStatus::FULL->value) {
            $order->cancellation_request = now();
            $order->cancelled_at         = now();
        }
        $order->save();
    }

}
