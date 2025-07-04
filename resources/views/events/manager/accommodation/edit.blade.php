<x-event-manager-layout :event="$event">

    @php
        $error = $errors->any();
    @endphp

    <x-slot name="header">
        <h2 class="event-h2 fs-4">

            <span>{{ $accommodation->hotel->name }}</span> &raquo;
            <span>Configuration</span>
        </h2>
        <x-back.topbar.edit-combo
            :event="$event"
            :index-route="route('panel.manager.event.accommodation.index', $event)"
            :use-create-route="false"
        />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <x-mfw::validation-banner/>
        <x-mfw::response-messages/>

        @if ($errors->has('deposit.amount.*'))
            <x-mfw::alert message="Les montants d'acompte doivent être tous des chiffres, au minium 1."/>
        @endif
        @if ($errors->has('deposit.paid_at.*'))
            <x-mfw::alert message="Les dates d'acompte doivent être toutes renseignées."/>
        @endif

        @include('events.manager.accommodation.tabs')

        <form method="post" action="{{ route('panel.manager.event.accommodation.update', [$event, $accommodation]) }}"
              id="wagaia-form">
            @csrf
            @method('PUT')

            <input type="hidden" name="event_lang" value="{!! current($event->flags) !!}"/>

            <div class="row pt-3">
                <div class="col-md-6">

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <x-mfw::checkbox name="accommodation[pec]" :switch="true" label="Éligible PEC" value="1"
                                             :affected="collect($error ? old('accommodation.pec') : $accommodation->pec)"/>
                        </div>
                        <div class="col-md-3">
                            <x-mfw::checkbox name="accommodation[published]" :switch="true" label="En ligne" :value="1"
                                             :affected="collect($error ? old('accommodation.published') : (int)(bool)$accommodation->published)"/>
                        </div>
                    </div>
                    <div class="mfw-line-separator mb-4"></div>

                    <x-mfw::translatable-tabs :fillables="$accommodation->fillables" :model="$accommodation"
                                              datakey="accommodation"/>

                    <h4>Hôtel ouvert à :</h4>
                    <div class="mfw-line-separator mb-4"></div>
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h4>Types de participation</h4>
                            <div id="participation_types_checker">
                                <x-mfw::checkbox name="allpt" value="1" label="Tout cocher"/>
                            </div>
                            <div id="participation_types">
                                <x-participation-types name="accommodation[participation_types]"
                                                       :subset="$event->participations->pluck('id')->toArray()"
                                                       :affected="collect($error ? old('accommodation.participation_types') : explode(',', $accommodation->participation_types))"
                                                       :filter="true"/>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <h4>TVA de l'hôtel</h4>
                            <x-mfw::select name="accommodation[vat_id]"
                                           :values="MetaFramework\Accessors\VatAccessor::readableArrayList()"
                                           :affected="$error ? old('accommodation.vat_id') : ($accommodation->vat_id ?: \MetaFramework\Accessors\VatAccessor::defaultRate()?->id)"
                                           :label="__('mfw-sellable.vat.label')" :nullable="false"/>

                            <h4 class="mt-4">Frais de dossier</h4>
                            <p class="text-secondary">Par chambre (par réservation)</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <x-mfw::input type="number" name="accommodation[processing_fee]"
                                                  :value="$error ? old('accommodation.processing_fee') : ($accommodation->processing_fee ?: 0)"
                                                  label="Montant € TTC"
                                                  :params="['step'=>'any']"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-mfw::select name="accommodation[processing_fee_vat_id]"
                                                   :values="MetaFramework\Accessors\VatAccessor::readableArrayList()"
                                                   :affected="$error ? old('accommodation.processing_fee_vat_id') : ($accommodation->processing_fee_vat_id ?: \MetaFramework\Accessors\VatAccessor::defaultRate()?->id)"
                                                   :label="__('mfw-sellable.vat.label')" :nullable="false"/>
                                </div>
                                <div class="col-12 mt-3">
                                    {!! \App\Helpers\Sage::renderSageInput(code:'roomtax', model:  $accommodation, label:$accommodation->sageFields()['roomtax'],prefix: true ) !!}
                                    {!! \App\Helpers\Sage::limitSageInput() !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mfw-line-separator mb-5"></div>

                    @include('events.manager.accommodation.inc.service')

                    <div class="mfw-line-separator mb-5"></div>

                    <h4>Acomptes</h4>
                    @include('events.manager.accommodation.inc.deposits')

                    <div class="mt-5">
                        <h4>Commission</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <x-mfw::number name="accommodation.comission"
                                               step="0.1"
                                               label="% Commission"
                                               :value="$error ? old('accommodation.comission') : $accommodation->comission"/>
                            </div>
                            <div class="col-md-4">
                                <x-mfw::number name="accommodation.comission_room"
                                               step="0.1"
                                               label="% Commission chambre"
                                               :value="$error ? old('accommodation.comission_room') : $accommodation->comission_room"/>
                            </div>
                            <div class="col-md-4">
                                <x-mfw::number name="accommodation.comission_breakfast"
                                               step="0.1"
                                               label="% Commission PDJ"
                                               :value="$error ? old('accommodation.comission_breakfast') : $accommodation->comission_breakfast"/>
                            </div>
                        </div>
                    </div>

                    <div class="row my-5">
                        <div class="col-12">
                            <h4>Après l'évènement</h4>
                        </div>
                        <div class="col-md-4">
                            <x-mfw::input type="number" name="accommodation[total_commission]"
                                          :value="$error ? old('accommodation.total_commission') : $accommodation->total_commission"
                                          label="Total commission € HT" :params="['step'=>'any']"/>
                        </div>
                        <div class="col-md-4">
                            <x-mfw::input type="number" name="accommodation[turnover]"
                                          :value="$error ? old('accommodation.turnover') : $accommodation->turnover"
                                          label="CA Total € HT" :params="['step'=>'any']"/>
                        </div>
                        <div class="col-md-4">
                            <x-mfw::input type="number" name="accommodation[total_cancellation]"
                                          :value="$error ? old('accommodation.total_cancellation') : $accommodation->total_cancellation"
                                          label="Montant des annulations € HT" :params="['step'=>'any']"/>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @include('events.manager.accommodation.inc.hotel_presentation')
                </div>
            </div>
        </form>
    </div>

    <x-mfw::save-alert/>

    @push('js')
        <script>
            $('#participation_types_checker :checkbox').click(function() {
               $('#participation_types :checkbox').prop('checked', $(this).is(':checked'));
            });
        </script>
    @endpush
</x-event-manager-layout>
