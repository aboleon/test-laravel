@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade show active"
     id="config-tabpane"
     role="tabpanel"
     aria-labelledby="config-tabpane-tab">
    <div class="row pt-3">
        <div class="col-md-6 pe-sm-5">
            <div>
                <x-mfw::translatable-tabs datakey="service_texts"
                                          :pluck="['title']"
                                          :fillables="$data->fillables"
                                          :model="$data"/>
            </div>
            <div class="row mb-4 align-items-center mfw-line-separator pb-4">
                <div class="col-md-3">
                    <x-mfw::checkbox name="service.published"
                                     :switch="true"
                                     label="En ligne"
                                     value="1"
                                     :affected="collect($error ? old('service.published') : ($data->id ? $data->published : 1))"/>
                </div>
                <div class="col-md-3">
                    @php
                        $is_pec = $error ? old('service.pec') : ($data->id ? $data->pec_eligible : 1);
                    @endphp
                    <x-mfw::checkbox name="service.pec"
                                     :switch="true"
                                     label="Éligible PEC"
                                     value="1"
                                     :affected="$is_pec"/>
                </div>
                <div class="col-md-5 service_pec_max_pax{{ $is_pec ? '' : ' invisible'}}">
                    <x-mfw::number min="1"
                                   name="service.pec_max_pax"
                                   label="Nb max par pers PEC"
                                   :value="$error ? old('service.pec_max_pax') : ($data->id ? $data->pec_max_pax : 1)"/>
                </div>
            </div>
            @php
                $is_invitation = old('service.is_invitation', $data->id ? $data->is_invitation : 0);
            @endphp
            <div class="row align-items-center mfw-line-separator pb-4 mb-4" x-data="{
                is_invitation: {{ $is_invitation?'true':'false' }},
            }">
                <div class="col-6">
                    <x-mfw::checkbox name="service.is_invitation"
                                     :switch="true"
                                     label="Prestation choix"
                                     value="1"
                                     :params="[
                                        'x-model' => 'is_invitation',
                                     ]"
                                     :affected="$is_invitation"/>
                </div>
                <div class="col-6" x-show="is_invitation" x-cloak x-transition>
                    <x-mfw::checkbox name="service.invitation_quantity_enabled"
                                     :switch="true"
                                     label="Quantité activée"
                                     value="1"
                                     :affected="$invitationQuantityEnabled"/>
                </div>
            </div>

            <div class="row mb-4 mfw-line-separator pb-4">
                <div class="col-xl-6">
                    <div class="mb-2">
                        <x-mfw::select name="service.service_group"
                                       :values="$event->services->pluck('name', 'id')->toArray()"
                                       label="Famille de prestations *"
                                       :affected="$error ? old('service.service_group') : $data->service_group"/>
                    </div>
                    <div>
                        <x-mfw::select name="service.service_group_combined"
                                       :values="$event->services->pluck('name', 'id')->toArray()"
                                       label="Prestation liée à une famille obligatoire"
                                       :affected="$error ? old('service.service_group_combined') : $data->service_group_combined"/>
                    </div>
                    <small>Doit être une famille différente de celle en cours pour une prise en
                        compte</small>
                </div>
                <div class="col-xl-6">
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div class="w-100 me-3">
                            <x-mfw::select name="service.place_id"
                                           label="Lieu"
                                           :values="$places"
                                           :affected="$error ? old('service.place_id') : $data->place_id"/>
                        </div>
                        <a href="#"
                           data-bs-toggle="modal"
                           data-bs-target="#mfwDynamicModal"
                           data-modal-shown="rebindDictionary"
                           data-modal-content-url="{{ route('panel.modal', ['requested' => 'createPlace', 'selectable' => 'service_place_id',]) }}"
                           class="fs-4 add-dynamic"><i class="fa-solid fa-circle-plus"></i></a>
                    </div>
                    <div id="salles" data-ajax="{{ route('ajax') }}">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div class="w-100 me-3">
                                <x-mfw::select name="service.room_id"
                                               :values="\App\Accessors\Places::selectableRoomsForPlace((int)$data->place_id)"
                                               :affected="$error ? old('service.room_id') : $data->room_id"
                                               label="Salle"/>
                            </div>
                            <a href="#"
                               id="create-room-for-place"
                               data-bs-toggle="modal"
                               data-bs-target="#mfwDynamicModal"
                               data-modal-content-url="{{ route('panel.modal', ['requested' => 'createPlaceRoom', 'selectable' => 'service_room_id', 'place_id' => $error ? old('service.place_id') : $data->place_id]) }}"
                               class="fs-4 add-dynamic"><i class="fa-solid fa-circle-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row mb-4 mfw-line-separator pb-4">
                <div class="row">
                    <div class="col-sm-4">
                        <x-mfw::datepicker name="service.service_date" label="Date"
                                           config="allowInput=true,defaultDate={{ $event->service_date }}"
                                           :value="$error ? old('service.service_date') : $data->service_date"/>
                    </div>
                    <div class="col-sm-4">
                        <x-mfw::input type="time" name="service.service_starts" label="Heure début"
                                      :value="$error ? old('service.service_starts') : $data->service_starts?->format('H:i')"/>
                    </div>
                    <div class="col-sm-4">
                        <x-mfw::input type="time" name="service.service_ends" label="Heure fin"
                                      :value="$error ? old('service.service_ends') : $data->service_ends?->format('H:i')"/>
                    </div>
                </div>
            </div>

            <div class="row mb-4 mfw-line-separator pb-4">

                <div class="col-md-3">
                    <x-mfw::number name="service.stock"
                                   label="Stock total"
                                   :value="($error ? old('service.stock') : $data->stock) ?: 0"/>
                </div>
                @php
                    $bookings = $sellableAccessor->getViewModel();
                @endphp
                <div class="col-md-3 text-dark">
                    <b class="d-block">Stock commandé</b><br>
                    {{ $bookings['bookings'] }}
                    @if($bookings['temp'])
                        <br>(+ {{ $bookings['temp'] }} en attente)
                    @endif
                </div>
                <div class="col-md-3 text-dark">
                    <b class="d-block">Stock restant réel</b><br>
                    {{ $bookings['available_label'] }}
                </div>
                <div class="col-md-3">
                    <x-mfw::number name="service.stock_showable"
                                   label="Stock affiché"
                                   :value="$error ? old('service.stock_showable') : $data->stock_showable"/>
                </div>
                <div class="col-md-3 pt-3 d-flex align-items-center">
                    <x-mfw::checkbox name="service.stock_unlimited"
                                     :switch="true"
                                     label="Stock illimité"
                                     value="1"
                                     :affected="collect($error ? old('service.stock_unlimited') : ($data->id ? $data->stock_unlimited : 1))"/>
                </div>

                <div class="col-12 pt-2">
                    <div class="text-secondary"><b class="text-secondary">* Stock affiché</b> :
                        Affichage sur le site du
                        nb de places restantes (En dessous de...)
                    </div>
                    <div class="text-secondary"><b class="text-secondary">* Stock illimité</b> :
                        Prend le dessus lorsque
                        activé
                    </div>
                </div>
            </div>

            <div class="row mb-4 mfw-line-separator pb-4 align-items-center">
                <div class="col-md-5">
                    <div class="my-2">
                        <x-mfw::checkbox name="sellable_has_deposit"
                                         :switch="true"
                                         label="Cette prestation nécessite caution"
                                         value="1"
                                         :affected="old('sellable_has_deposit', !is_null($data->deposit))"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <x-mfw::number name="sellable_deposit"
                                   label="Montant"
                                   :value="$error ? old('sellable_deposit') : $data->deposit?->amount"/>
                </div>
                <div class="col-md-3">
                    <x-mfw::select name="sellable_deposit_vat_id"
                                   :values="MetaFramework\Accessors\VatAccessor::readableArrayList()"
                                   :affected="old('sellable_deposit_vat_id', $data->deposit?->vat_id ?? \MetaFramework\Accessors\VatAccessor::defaultRate()?->id)"
                                   label="TVA sur caution si facturée" :nullable="false"/>
                </div>
            </div>

        </div>
        <div class="col-md-6 ps-sm-5">
            @include('events.manager.sellable.inc.prices')
        </div>
    </div>
    @include('lib.select2')
    @push('callbacks')
        <script>
            function appendPlaceRoom(result) {
                let rooms = $('#service_room_id');
                rooms.find('option').not(':first').remove();
                console.log(typeof result.rooms);
                if (result.rooms) {
                    let html = '';
                    for (const [key, value] of Object.entries(result.rooms)) {
                        html = html.concat('<option value="' + key + '">' + value + '</option>');
                    }
                    rooms.append(html);
                }
            }
        </script>
    @endpush
    @push('js')
        <script>
            $(function () {
                $('#service_pec').click(function () {
                    $('.service_pec_max_pax').toggleClass('invisible');
                });

                $('#service_place_id').select2().change(function () {
                    $('#salles .messages').html('');
                    ajax('action=fetchPlaceRoomForPlace&place_id=' + $(this).val() + '&callback=appendPlaceRoom', $('#salles'));

                    let roomCreator = $('#create-room-for-place');
                    const url = new URL(roomCreator.data('modal-content-url'));
                    const params = url.searchParams;
                    params.set('place_id', $('#service_place_id :selected').val());
                    url.search = params.toString();
                    roomCreator.attr('data-modal-content-url', url.toString()).change();

                });

                $('.mass_checker .checker :checkbox').click(function () {
                    $(this).closest('.mass_checker').find(':checkbox').not(this).prop('checked', $(this).is(':checked'));
                });
            });
        </script>
    @endpush

    @push("css")
        <style>
            .stock-limit-1 input {
                width: 90px !important;
            }
        </style>
    @endpush
</div>
