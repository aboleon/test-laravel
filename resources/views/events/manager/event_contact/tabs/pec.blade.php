@php
    $error = $errors->any();
@endphp

<div class="tab-pane fade pt-4" id="pec-tabpane" role="tabpanel" aria-labelledby="pec-tabpane-tab">

    <header class="wg-card mfw-line-separator mb-3">
        <h4>PEC</h4>
    </header>


    @if ($eventContact->is_pec_eligible)

        @if($eventContact->pec_enabled)
            <x-mfw::notice
                :message="'Ce participant est PEC. Les frais de dossier ' . ($eventContact->pec_fees_apply ? 'sont' : 'ne sont pas') . ' affectés.'"/>
            @if ($eventContactAccessor->hasPaidGrantDeposit())
                <x-mfw::alert class="mt-2" type="success"
                              :message="$eventContactAccessor->isExemptGrantFromDeposit() ? 'Ce participant est dispensé de caution.' : 'Ce participant a réglé sa caution.'"/>
            @else
                <x-mfw::alert class="mt-2 simplified"
                              message="Ce participant n'a pas encore réglé sa caution. Le financement effectif n'est pas possible."/>
            @endif
        @else

            <form id="form_event_contact_pec" method="post" action="{{route('panel.manager.event.event_contact.update', [
                        'event' => $event,
                        'event_contact' => $eventContact,
                        ])}}">
                @csrf
                @method('PUT')

                <input type="hidden" name="section" value="pec"/>

                @if(!$accountAccessor->hasValidBillingAddress())
                    <x-mfw::alert message="Ce parrticipant n'as pas une adresse de facturation valide. Veuillez corriger ce point avant de continuer." />
                @else

                    @php
                        $grant = $eventContactAccessor->getPreferedGrantNormalizedData();
                    @endphp
                    @if(isset($grant['error']))
                        {!! MetaFramework\Accessors\ResponseParser::parseResponse($grant) !!}
                    @else
                        <input type="hidden" name="preferred_grant[id]" value="{{ $grant['grant']['id'] }}">
                        <input type="hidden" name="preferred_grant[deposit]" value="{{ $grant['grant']['deposit'] }}">
                        <input type="hidden" name="preferred_grant[title]" value="{{ $grant['grant']['title'] }}">
                        <input type="hidden" name="preferred_grant[vat_id]" value="{{ $grant['grant']['vat_id'] }}">

                        <h6 class="fw-bold text-dark">
                            Ce participant est éligible à la prise en charge :
                        </h6>
                        <div class="alert alert-success">

                            <x-mfw::checkbox name="pec_enabled"
                                             label="Valider la prise en charge (obligatoire pour affecter des inscriptions PEC / hébergement PEC / transport PEC)"
                                             value="1"/>
                            <x-mfw::checkbox name="no_pec_fee" label="Ne pas affecter les frais de dossier" value="1"
                                             :affected="$eventContact->id ? !$eventContact->pec_fees_apply : null"/>
                            <x-mfw::checkbox name="no_deposit" label="Ne pas affecter de caution" value="1"/>

                        </div>
                    @endif
                @endif

            </form>
        @endif
        <div class="row pt-3">
            @include('events.manager.event_contact.inc.pec-datatable')
        </div>
    @else
        <div class="row pt-3">
            <div class="alert alert-warning">
                Ce participant n'est pas éligible à la prise en charge.
            </div>
        </div>
    @endif


</div>
