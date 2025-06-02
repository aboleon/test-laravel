<?php

namespace App\Actions\EventManager;

use App\Accessors\Accounts;
use App\Accessors\EventContactAccessor;
use App\Accessors\EventManager\Grant\GrantAccessor;
use App\Actions\Order\PecActions;
use App\Enum\EventDepositStatus;
use App\Enum\OrderClientType;
use App\Enum\OrderMarker;
use App\Enum\OrderOrigin;
use App\Enum\OrderType;
use App\Models\CustomPaymentCall;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Grant\GrantTransportDistribution;
use App\Models\EventManager\Transport\EventTransport;
use App\Models\FrontCart;
use App\Models\FrontCartLine;
use App\Models\Order;
use App\Models\Order\Cart\GrantDepositCart;
use App\Models\Order\EventDeposit;
use App\Models\PecDistribution;
use App\Services\PaymentProvider\PayBox\Paybox;
use App\Services\Pec\PecFinder;
use App\Services\Pec\PecParser;
use App\Traits\Models\EventContactModelTrait;
use App\Traits\Models\EventGrantModelTrait;
use App\Traits\Models\EventTransportModelTrait;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MetaFramework\Accessors\Prices;
use MetaFramework\Accessors\VatAccessor;
use MetaFramework\Traits\Ajax;
use Str;
use Throwable;

class GrantActions
{
    use Ajax;
    use EventTransportModelTrait;
    use EventGrantModelTrait;

    protected EventDeposit $deposit;
    protected CustomPaymentCall $paymentCall;


    public function updateEligibleStatusForContacts(Event $event): self
    {
        try {
            $event->load('contacts.profile', 'contacts.address');

            $pec = new PecParser($event, $event->contacts);
            $pec->calculate();

            $nonEligibleContacts = $pec->getNonEligibleContactIds();


            EventContact::whereIn('id', $pec->getEligibileContactIds())->update(['is_pec_eligible' => 1]);
            EventContact::whereIn('id', $nonEligibleContacts)->update(['is_pec_eligible' => null]);

            // Remove PEC eligibility for pending front Carts for NonEligibleContacts

            if ($nonEligibleContacts->isNotEmpty()) {
                FrontCartLine::whereIn(
                    'front_cart_id',
                    FrontCart::where(fn($q) => $q->whereIn('event_contact_id', $nonEligibleContacts))->whereNull('order_id')->pluck('id'),
                )
                    ->where('total_pec', '>', 0)
                    ->update(['total_pec' => 0]);
            }
            $this->responseSuccess("La mise à jour de l'égilibité Grant des contacts est effectuée.");
        } catch (Throwable) {
            $this->responseError("La mise à jour de l'égilibité Grant des contacts à échoué.");
        }

        return $this;
    }

    public function updateEligibleStatusForSingleContact(Event $event, EventContact $contact): self
    {
        try {
            $pecParser = new PecParser($event, collect()->push($contact));
            $pecParser->calculate();

            $contact->is_pec_eligible = $pecParser->hasGrants($contact->id) ? 1 : null;
            $contact->save();

            // Reset PEC pending carts
            if (is_null($contact->is_pec_eligible)) {
                FrontCartLine::where(
                    'front_cart_id',
                    FrontCart::where('event_contact_id', $contact->id)->whereNull('order_id')->value('id'),
                )
                    ->where('total_pec', '>', 0)
                    ->update(['total_pec' => 0]);
            }

            $this->responseSuccess("La mise à jour de l'égilibité Grant est effectuée.");
        } catch (Throwable) {
            $this->responseError("La mise à jour de l'égilibité Grant à échoué.");
        }

        return $this;
    }

    public function setEligiblesToNull(Event $event): self
    {
        // Update event contacts first
        $eventContactQuery = EventContact::where('event_id', $event->id);
        $eventContactQuery->update(['is_pec_eligible' => null]);

        // Get event contact IDs
        $eventContactIds = EventContact::where('event_id', $event->id)->pluck('id');

        // Get cart IDs from front_carts table
        FrontCartLine::whereIn(
            'front_cart_id',
            FrontCart::whereIn('event_contact_id', $eventContactIds)
                ->whereNull('order_id')
                ->pluck('id')
        )
            ->where('total_pec', '>', 0)
            ->update(['total_pec' => 0]);

        $this->responseSuccess("Le statut éligibilité de tous les contacts a été remis à zéro.");

        return $this;
    }

    /**
     * Création d'une caution depuis le BO
     *
     * @param  array         $grant  [id,title,deposit,vat_id]
     * @param  EventContact  $eventContact
     *
     * @return self
     */
    public function attachDepositToEventContact(array $grant, EventContact $eventContact): self
    {
        // Vérifier s'il n'y a pas déjà une caution
        if ($eventContact->grantDeposit) {
            $this->deposit = $eventContact->grantDeposit;

            // Si c'est une TEMP (front)
            if ($this->deposit->status == EventDepositStatus::TEMP->value) {
                $this->deposit->status = EventDepositStatus::default();
                $this->deposit->save();

                $this->makePaymentCall();

                $this->responseNotice(
                    "Une caution de ".$this->deposit->total_net + $this->deposit->total_vat." € du grant ".$this->deposit->shoppable_label." a été rattachée pour paiement. Vous pouvez <a href='".route('custompayment.form', ['uuid' => Crypt::encryptString($this->paymentCall->id)])."' target='_blank' class='btn btn-sm btn-secondary'>voir la page de paiement</a> ou envoyer le lien de paiement depuis la page des <a target='_blank' class='btn btn-sm btn-secondary' href='".route(
                        'panel.manager.event.event_deposit.index',
                        $eventContact->event_id,
                    )."'>cautions</a>",
                );

                return $this;
            }

            $eventContactAccessor = (new EventContactAccessor())->setEventContact($eventContact);
            $message              = "Ce participant a déjà une caution Grant ";

            if ($eventContactAccessor->hasPaidGrantDeposit()) {
                $message .= " et elle a été payée.";
                if ( ! $eventContact->pec_enabled) {
                    $eventContact->pec_enabled = 1;
                    $eventContact->save();
                    $this->responseSuccess($message);

                    return $this;
                }
            } elseif ($this->deposit->status == EventDepositStatus::REFUNDED->value) {
                $message .= " et elle a remboursée.";
            } else {
                $message .= "qui est en attente de paiement.";
            }

            $this->responseError($message);

            return $this;
        }

        try {
            $this->makeGrantDeposit($eventContact, $grant);

            if ($this->hasErrors()) {
                return $this;
            }

            // Create Deposit Payment Call
            $this->makePaymentCall();

            $this->responseNotice(
                "Une caution de ".$grant['deposit']." € du grant ".$grant['title']." a été rattachée pour paiement. Vous pouvez <a href='".route('custompayment.form', ['uuid' => Crypt::encryptString($this->paymentCall->id)])."' target='_blank' class='btn btn-sm btn-secondary'>voir la page de paiement</a> ou envoyer le lien de paiement depuis la page des <a target='_blank' class='btn btn-sm btn-secondary' href='".route('panel.manager.event.event_deposit.index', $eventContact->event_id)
                ."'>cautions</a>",
            );
        } catch (Throwable $e) {
            $this->responseException($e, "La caution grant n'a pas pu être rattachée. Activation PEC annulée.");
        }

        return $this;
    }

    private function makePaymentCall(): self
    {
        $this->paymentCall = new CustomPaymentCall([
            'provider' => (new Paybox())->signature()['id'],
            'total'    => $this->deposit->total_net + $this->deposit->total_vat,
        ]);

        $this->deposit->paymentCall()->save($this->paymentCall);
        Log::info('CustomPaymentCall generated.');

        return $this;
    }

    public function makeGrantDeposit(EventContact $eventContact, array $grant, string $state = EventDepositStatus::UNPAID->value): self
    {
        // Invoiceable
        $address = (new Accounts($eventContact->account))->billingAddress();

        if ( ! $address) {
            $this->responseError("La caution grant ne peut pas être rattachée car ce participant n'a pas d'adresse de facturation. Activation PEC annulée.");

            return $this;
        }

        $vat_id = $grant['vat_id'] ?? VatAccessor::defaultId();

        // Create Order
        $order              = new Order();
        $order->uuid        = Str::uuid();
        $order->marker      = OrderMarker::GHOST->value;
        $order->origin      = OrderOrigin::BACK->value;
        $order->type        = OrderType::GRANTDEPOSIT->value;
        $order->event_id    = $eventContact->event_id;
        $order->client_id   = $eventContact->user_id;
        $order->client_type = OrderClientType::CONTACT->value;
        $order->total_net   = VatAccessor::netPriceFromVatPrice($grant['deposit'], $vat_id);
        $order->total_vat   = VatAccessor::vatForPrice($grant['deposit'], $vat_id);
        $order->created_by  = auth()->id();
        $order->save();

        $order->invoiceable()->save(new Order\Invoiceable([
            'account_id'    => $eventContact->user_id,
            'account_type'  => OrderClientType::CONTACT->value,
            'address_id'    => $address->id,
            'company'       => $address->company,
            'first_name'    => $eventContact->account->first_name,
            'last_name'     => $eventContact->account->last_name,
            'postal_code'   => $address->postal_code,
            'country_code'  => $address->country_code,
            'street_number' => $address->street_number,
            'locality'      => $address->locality,
            'cedex'         => $address->cedex,
            'route'         => $address->route,
            'text_address'  => $address->text_address,
        ]));


        // Create deposit
        $this->deposit = $order->deposits()->save(
            new EventDeposit([
                'event_id'         => $eventContact->event_id,
                'shoppable_id'     => $grant['id'],
                'shoppable_type'   => OrderType::GRANTDEPOSIT->value,
                'vat_id'           => $vat_id,
                'total_net'        => VatAccessor::netPriceFromVatPrice($grant['deposit'], $vat_id),
                'total_vat'        => VatAccessor::vatForPrice($grant['deposit'], $vat_id),
                'event_contact_id' => $eventContact->id,
                'status'           => $state,
                'shoppable_label'  => $grant['title'],
            ]),
        );


        // Create deposit shopping cart
        $order->grantDeposit()->save(
            new GrantDepositCart([
                'event_deposit_id' => $this->deposit->id,
                'event_grant_id'   => $grant['id'],
                'vat_id'           => $vat_id,
                'unit_price'       => $grant['deposit'],
                'total_net'        => VatAccessor::netPriceFromVatPrice($grant['deposit'], $vat_id),
                'total_vat'        => VatAccessor::vatForPrice($grant['deposit'], $vat_id),
                'event_contact_id' => $eventContact->id,
                'quantity'         => 1,
            ]),
        );

        return $this;
    }

    public function getDeposit(): EventDeposit
    {
        return $this->deposit;
    }

    /**
     * @throws Exception
     */
    public function fetchTransportableGrant(null|int|EventTransport $event_transport_id): self
    {
        $this->setEventTransport($event_transport_id);
        $this->validateModelProperty('eventTransport', __('errors.event_transport_not_found'));

        if ($this->hasErrors()) {
            return $this;
        }

        $eventContactAccessor = (new EventContactAccessor())->setEventContact($this->eventTransport->events_contacts_id);

        if ( ! $eventContactAccessor->isPecAuthorized()) {
            $this->responseError('Ce contact n\'est pas PEC');

            return $this;
        }

        $pec = (new PecActions());

        $pec->setEventContact($eventContactAccessor->getEventContact());
        $pec->setEvent($pec->getEventContact()->event);


        $pec->pecParser();
        if ($pec->getPecParser()->hasGrants($pec->getEventContact()->id)) {
            $pec->pecFinder();

            $pecFinder = (new PecFinder())
                ->setEventContact($pec->getEventContact())
                ->setTransportFeesWhitoutTax($this->eventTransport->price_before_tax)
                ->setTransportFeesWithTax($this->eventTransport->price_after_tax)
                ->setGrants($pec->getPecParser()->getGrantsFor($pec->getEventContact()->id));

            $pec->setPecDistributionResult($pecFinder->filterGrants());

            if ($pec->getPecDistributionResult()->isCovered()) {
                $grant = $pec->getPecDistributionResult()->getDistribution();
                $this->responseElement('grant', $grant);
            } else {
                $this->responseWarning("Aucun financement grant n'est disponible");
            }

            return $this;
        }

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function saveTransportableGrant(): self
    {
        $this->setEventTransport((int)request('event_transport_id'));
        $this->validateModelProperty('eventTransport', __('errors.event_transport_not_found'));

        $this->setEventGrant((int)request('grant_id'));
        $this->validateModelProperty('eventGrant', __('errors.event_grant_not_found'));

        $cost = (int)request('cost');

        if ( ! $cost) {
            $this->responseError('Le coût du financement est obligatoire');
        }

        if ($this->hasErrors()) {
            return $this;
        }

        $grantAccessor = (new GrantAccessor())->setEventGrant($this->eventGrant);

        $budget = $grantAccessor->availableAmount();

        if ($budget >= $cost) {
            $data = (new GrantTransportDistribution())
                ->setCost($cost)
                ->setEventGrant($this->eventGrant)
                ->setEventContact($this->getEventTransport()->events_contacts_id)
                ->setEventTransport($this->getEventTransport());

            try {
                $this->pushMessages(
                    (new PecActions())
                        ->registerTransportDistribution($data),
                );
            } catch (Throwable $e) {
                $this->responseException($e);
            }

            return $this;
        }

        $this->responseError("Le financement n'est plus disponible");
        return $this;

    }

    public function removeTransportableGrant(int $distribution_id): self
    {
        DB::beginTransaction();
        try {
            PecDistribution::where('id', $distribution_id)->delete();
            $this->responseSuccess("Suppression du financement effectué");
            DB::commit();
        } catch (Throwable $e) {
            $this->responseException($e);
        }
        return $this;
    }
}
