<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\EventContactAccessor;
use App\Accessors\Front\FrontCache;
use App\Accessors\Front\Program\Interventions;
use App\Actions\Order\PecActions;
use App\Enum\DesiredTransportManagement;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Http\Requests\Front\Transport\TransportDepartureStepFormRequest;
use App\Http\Requests\Front\Transport\TransportDivineStepInfoFormRequest;
use App\Http\Requests\Front\Transport\TransportReturnStepFormRequest;
use App\Http\Requests\Front\Transport\TransportTransferStepFormRequest;
use App\Models\Event;
use App\Models\EventManager\Transport\EventTransport;
use App\Services\Pec\PecFinder;
use App\Traits\Models\EventTransportModelTrait;
use Carbon\Carbon;
use Exception;

class TransportController extends EventBaseController
{
    use EventTransportModelTrait;

    /**
     * @throws Exception
     */
    public function edit(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.transport'));

        $step         = request('step', 0);
        $eventContact = $this->getEventContact()->load('transport');
        $transport    = $eventContact->transport;

        $view                 = 'front.user.transport'.($transport?->desired_management ? '.type_'.$transport->desired_management : '');
        $eventContactAccessor = (new EventContactAccessor())->setEvent($event)->setEventContact($eventContact);

        return view($view, [
            "step"                 => $step,
            "eventContact"         => $eventContact,
            "eventContactAccessor" => $eventContactAccessor,
            'isTransferEligible'   => $eventContactAccessor->isEligibleForTransfer(),
            "transport"            => $transport,
            'moderatorOratorItems' => Interventions::getOratorModeratorItems($eventContact),
            "event"                => $event,
        ]);
    }

    public function update(string $locale, Event $event)
    {
        $eventContact      = FrontCache::getEventContact($event->id, auth()->id());
        $desiredManagement = request("desired_management");

        $transport = $eventContact->transport;
        if (null === $transport) {
            $transport = new EventTransport();

            $transport->events_contacts_id = $eventContact->id;
            $transport->desired_management = $desiredManagement;

            if (DesiredTransportManagement::UNNECESSARY->value === $desiredManagement) {
                $transport->transfer_requested = 0;
            }

            $transport->save();

            return redirect()->route('front.event.transport.edit', ['locale' => $locale, 'event' => $event]);
        } else {
            $error = "Transport already exists for this event contact.";

            return redirect()->route('front.event.transport.edit', ['locale' => $locale, 'event' => $event])->withErrors($error);
        }
    }


    public function updateParticipantStepDocuments(string $local, Event $event)
    {
        // Perform the validation
        $validatedData = request()->validate([
            'ticket_price' => 'required|numeric|gt:0',
        ], [

            'ticket_price.required' => __('validation.required', ['attribute' => __('front/transport.labels.tickets_price')]),
            'ticket_price.numeric'  => __('validation.numeric', ['attribute' => __('front/transport.labels.tickets_price')]),
            'ticket_price.gt'       => __('validation.gt.numeric', ['attribute' => __('front/transport.labels.tickets_price')]),
        ]);

        $transport = $this->getTransportInstanceByEvent();



        $transport->ticket_price = $validatedData['ticket_price'];
        $transport->save();

        return redirect()->route('front.event.transport.edit', [
            'locale' => $local,
            'event'  => $event,
            'step'   => 4,
        ]);
    }

    public function updateParticipantStepDeparture(TransportDepartureStepFormRequest $request, string $local, Event $event)
    {
        return $this->updateDepartureStep($request, $local, $event, 2);
    }

    public function updateParticipantStepReturn(TransportReturnStepFormRequest $request, string $local, Event $event)
    {
        return $this->updateReturnStep($request, $local, $event, 3);
    }

    public function updateParticipantStepTransfer(TransportTransferStepFormRequest $request, string $local, Event $event)
    {
        return $this->updateTransferStep($request, $local, $event);
    }

    public function updateParticipantStepRecap(string $local, Event $event)
    {
        return $this->updateRecapStep($local, $event);
    }

    /**
     * @throws Exception
     */
    public function updateDivineStepInfo(TransportDivineStepInfoFormRequest $request, string $local, Event $event)
    {
        $data = $request->validated();
        if ($data['birth']) {
            $data['birth'] = Carbon::createFromFormat(config('app.date_display_format'), $data['birth'])->format("Y-m-d");
        }

        $account = FrontCache::getAccount();
        $profile = $account->profile;
        $profile->fill([
            'passport_first_name' => $data['passport_first_name'],
            'passport_last_name'  => $data['passport_last_name'],
            'birth'               => $data['birth'],
        ]);
        $profile->save();

        $transport = $this->getTransportInstanceByEvent();
        $transport->fill([
            'travel_preferences' => $data['travel_preferences'],
        ]);
        $transport->save();

        return redirect()->route('front.event.transport.edit', [
            'locale' => $local,
            'event'  => $event,
            'step'   => 2,
        ]);
    }


    public function updateDivineStepDeparture(TransportDepartureStepFormRequest $request, string $local, Event $event)
    {
        return $this->updateDepartureStep($request, $local, $event, 3);
    }

    public function updateDivineStepReturn(TransportReturnStepFormRequest $request, string $local, Event $event)
    {
        return $this->updateReturnStep($request, $local, $event, 4);
    }

    public function updateDivineStepTransfer(TransportTransferStepFormRequest $request, string $local, Event $event)
    {
        return $this->updateTransferStep($request, $local, $event);
    }

    public function updateDivineStepRecap(string $local, Event $event)
    {
        return $this->updateRecapStep($local, $event);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @throws Exception
     */
    private function getTransportInstanceByEvent(): EventTransport
    {
        $eventContact = $this->getEventContact();
        $transport    = $eventContact->transport;
        if ( ! $transport) {
            $transport                     = new EventTransport();
            $transport->events_contacts_id = $eventContact->id;
        }

        return $transport;
    }


    private function updateDepartureStep(TransportDepartureStepFormRequest $request, string $local, Event $event, int $nextStep)
    {
        $data = $request->validated();
        if ($data['departure_start_date']) {
            $data['departure_start_date'] = Carbon::createFromFormat(config('app.date_display_format'), $data['departure_start_date'])->format("Y-m-d");
        }

        if ($data['departure_start_time']) {
            $data['departure_start_time'] = $data['departure_start_time'].':00';
        }
        if ($data['departure_end_time']) {
            $data['departure_end_time'] = $data['departure_end_time'].':00';
        }


        $transport = $this->getTransportInstanceByEvent();
        $transport->fill($data);
        $transport->save();

        return redirect()->route('front.event.transport.edit', [
            'locale' => $local,
            'event'  => $event,
            'step'   => $nextStep,
        ]);
    }


    private function updateReturnStep(TransportReturnStepFormRequest $request, string $local, Event $event, int $nextStep)
    {
        $data = $request->validated();

        if ($data['return_start_date']) {
            $data['return_start_date'] = Carbon::createFromFormat(config('app.date_display_format'), $data['return_start_date'])->format("Y-m-d");
        }
        if ($data['return_start_time']) {
            $data['return_start_time'] = $data['return_start_time'].':00';
        }
        if ($data['return_end_time']) {
            $data['return_end_time'] = $data['return_end_time'].':00';
        }


        $transport = $this->getTransportInstanceByEvent();
        $transport->fill($data);
        $transport->save();

        return redirect()->route('front.event.transport.edit', [
            'locale' => $local,
            'event'  => $event,
            'step'   => $nextStep,
        ]);
    }


    private function updateTransferStep(TransportTransferStepFormRequest $request, string $local, Event $event)
    {
        $data = $request->validated();

        $transport = $this->getTransportInstanceByEvent();
        $transport->fill($data);
        $transport->save();

        return redirect()->route('front.event.transport.edit', [
            'locale' => $local,
            'event'  => $event,
            'step'   => 5,
        ]);
    }


    /**
     * @throws Exception
     */
    public function updateRecapStep(string $local, Event $event)
    {
        $transport = $this->getTransportInstanceByEvent();
        $transport->fill([
            'request_completed' => 1,
        ]);
        $transport->save();

        $eventContactAccessor = (new EventContactAccessor())->setEventContact($this->getEventContact());

        // Fetch Grant and set max / min

        if ($eventContactAccessor->isPecAuthorized()) {
            $pec = (new PecActions());

            $pec->setEventContact($this->getEventContact());
            $pec->setEvent($event);


            $pec->pecParser();

            if ($pec->getPecParser()->hasGrants($pec->getEventContact()->id)) {
                $pec->pecFinder();

                $pecFinder = (new PecFinder())
                    ->setEventContact($pec->getEventContact())
                    ->setTransportFeesWhitoutTax(0)
                    ->setTransportFeesWithTax(0)
                    ->setGrants($pec->getPecParser()->getGrantsFor($pec->getEventContact()->id));

                $pec->setPecDistributionResult($pecFinder->filterGrants());

                if ($pec->getPecDistributionResult()->isCovered()) {
                    //$grant                       = $pec->getPecDistributionResult()->getDistribution();
                    $transport->price_before_tax = $transport->ticket_price;
                    $transport->price_after_tax  = $transport->ticket_price;
                    $transport->save();
                    //$this->responseElement('grant', $grant);
                }
                /* else {
                    $this->responseWarning("Aucun financement grant n'est disponible");
                } */
            }
        }

        return redirect()->route('front.event.transport.edit', [
            'locale' => $local,
            'event'  => $event,
        ]);
    }
}
