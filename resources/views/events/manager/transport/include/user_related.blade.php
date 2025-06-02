@php
    use App\Accessors\GroupAccessor;
    use App\Accessors\EventContactAccessor;
    use App\Enum\DesiredTransportManagement;
    use MetaFramework\Accessors\Prices;
@endphp
    <!-- participant, gestion demandée -->
<div class="row mb-3 align-items-start">
    <div class="col-md-6 mb-3 d-flex align-items-center">

        <div class="flex-grow-1">

        
            <label for="autocomplete-participant" class="form-label">Participant</label>
            <input type="text" id="autocomplete-participant"
                   data-ajax="{!! route('ajax', [
                                                    'action' => 'searchParticipants',
                                                    'eventId' => $event->id,
                                                ]) !!}"
                   class="form-control"
                   data-id="{{ $eventContact?->id }}"
                   value="{{$eventContact?$eventContact->user->last_name . ' ' . $eventContact->user->first_name:''}}"
            >
            @if ($eventContact)
                <div class="d-sm-flex align-items-center mt-2">
                    <a class="btn btn-sm btn-secondary"
                       href="{{ route('panel.manager.event.event_contact.edit', ['event' => $event, 'event_contact' => $eventContact]) }}">Dashboard</a>

                    @if ($eventContactAccessor->isPecAuthorized())
                        <x-enabled-mark :enabled="true"
                                        label="PEC"/>
                    @endif
                </div>
            @endif
            <input type="hidden" name="item[main][event_contact_id]"
                   id="autocomplete-participant-hidden-input"
                   value="{{$eventContact?->id}}">

        </div>
        @if(
            !$eventContact &&
            \Illuminate\Support\Facades\Route::currentRouteName() !== 'panel.manager.event.transport.create'
        )
            <a class="btn btn-sm btn-success ms-2 mt-4"
               href="{{ route('panel.accounts.create') }}">
                <i class="fa-solid fa-circle-plus"></i> Créer un compte</a>
        @endif
    </div>
    <div class="col-md-6">
        @if($eventContact?->id)
            <x-mfw::select name="item[main][desired_management]"
                           label="{{__('transport.labels.desired_transport_management')}}"
                           :values="DesiredTransportManagement::translations()"
                           :affected="$error ? old('item.main.desired_management') : $transport?->desired_management"/>

            @if ($transportAccessor->managementHasChanged())
                <div class="mt-3 row align-items-start">
                    <div class="col-md-6">
                        <p class="m-0">Le mode de gestion précédent était
                            <b>{{ DesiredTransportManagement::translated($transport->management_history) }}</b>
                        </p>
                        <p>
                            {{ $transportAccessor->managementChangeWasNotified()
                                ? "Une notification a été envoyée le " . $transport->management_mail?->format('d/m/Y à H:i')
                                : "La personne n'a pas encore été notifiée"
                            }}
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <x-mfw::simple-modal id="send_management_mail"
                                             class="btn btn-sm btn-secondary"
                                             title="Envoi d'une notification de changement de gestion du dossier transport"
                                             confirm="Envoyer"
                                             body="Cette action enverra un e-mail à la personne pour la notifier du changement du mode de gestion de son dossier transport de <b class=text-danger>{{ DesiredTransportManagement::translated($transport->management_history) .'</b> à <b class=text-success>'. DesiredTransportManagement::translated($transport->desired_management) }}</b>."
                                             callback="sendManagementMail"
                                             :modelid="$transport->id"
                                             text='Notifier le changement de mode de gestion'/>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<!-- nom prénom passeport -->
<div class="row mb-3 tr-base">
    <div class="col-md-4">
        <x-mfw::input name="item[account][passport_last_name]"
                      :value="old('item.account.passport_last_name', $account?->profile?->passport_last_name)"
                      :label="__('forms.fields.passport_last_name')"/>
    </div>
    <div class="col-md-4">
        <x-mfw::input name="item[account][passport_first_name]"
                      :value="old('item.account.passport_first_name', $account?->profile?->passport_first_name)"
                      :label="__('forms.fields.passport_first_name')"/>
    </div>
    <div class="col-md-4">
        <x-mfw::datepicker name="item[account][birth_date]"
                           label="{{__('account.birth')}}"
                           value=""
                           config="dateFormat={{ config('app.date_display_format')}},defaultDate={{$error ? old('item.account.birth') : \App\Accessors\Chronos::formatDate($account?->profile->birth) }}"/>
    </div>
</div>
