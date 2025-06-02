<div class="tab-pane fade" id="transport-tabpane" role="tabpanel" aria-labelledby="transport-tabpane-tab">
    <div class="row gx-5">
        <div class="col-12">
            <div class="row pt-4 gy-4">
                <div class="col-12">
                    <h4 class="mb-1">Activation de la gestion transport</h4>
                </div>
                <div class="col-lg-4 mb-1">

                    <div class="d-flex">
                        <x-mfw::checkbox :switch="true"
                                         name="event.transport.{{ \App\Enum\ParticipantType::ORATOR->value }}"
                                         value="1"
                                         label="Orateurs"
                                         :affected="in_array(\App\Enum\ParticipantType::ORATOR->value, (array)old('event.transport', $data->transport))"/>

                        <x-mfw::checkbox :switch="true"
                                         name="event.transfert.{{ \App\Enum\ParticipantType::ORATOR->value }}"
                                         class="ms-2"
                                         value="1"
                                         label="Transferts"
                                         :affected="in_array(\App\Enum\ParticipantType::ORATOR->value, (array)old('event.transfert', $data->transfert))"/>
                    </div>
                </div>

                <div class="col-lg-4 mb-1">
                    <div class="d-flex">

                        <x-mfw::checkbox :switch="true"
                                         name="event.transport.pec"
                                         value="1"
                                         label="PEC"
                                         :affected="in_array('pec', (array)old('event.transport', $data->transport))"/>

                        <x-mfw::checkbox :switch="true"
                                         name="event.transfert.pec"
                                         class="ms-2"
                                         value="1"
                                         label="Transferts"
                                         :affected="in_array('pec', (array)old('event.transfert', $data->transfert))"/>
                    </div>
                </div>

                <div class="col-lg-4 mb-1">
                    <div class="d-flex">

                        <x-mfw::checkbox :switch="true"
                                         name="event.transport.{{ \App\Enum\ParticipantType::CONGRESS->value }}"
                                         value="1"
                                         label="Congressistes"
                                         :affected="in_array(\App\Enum\ParticipantType::CONGRESS->value, (array)old('event.transport', $data->transport))"/>

                        <x-mfw::checkbox :switch="true"
                                         name="event.transfert.{{ \App\Enum\ParticipantType::CONGRESS->value }}"
                                         class="ms-2"
                                         value="1"
                                         label="Transferts"
                                         :affected="in_array(\App\Enum\ParticipantType::CONGRESS->value, (array)old('event.transfert', $data->transfert))"/>
                    </div>
                </div>

                <div class="mfw-line-separator col-12"></div>
                <div class="col-sm-4">
                    <x-mfw::checkbox name="event.config.manage_transport_upfront"
                                     :switch="true"
                                     label="Autoriser la gestion transport en front"
                                     value="1"
                                     :affected="old('event.config.manage_transport_upfront', $data->manage_transport_upfront)"/>
                </div>
                <div class="col-md-8 mb-3">
                    <x-mfw::datepicker
                        label="Date limite pour le prix des billets<br><span class='text-danger'>Si gestion libre par l'internaute</span>"
                        name="event[config][transport_tickets_limit_date]"
                        :value="$error ? old('event.config.transport_tickets_limit_date') : $data->transport_tickets_limit_date"/>
                </div>
            </div>
            <div class="mfw-line-separator mt-4 mb-4"></div>

        </div>
        <div class="col-12">
            <x-mfw::translatable-tabs :fillables="$data->fillables['transport']" id="transport_texts"
                                      datakey="event[texts]" :model="$texts"/>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $('input[id^="event_transfert"]').click(function () {
                if (!$(this).parent().prev().find(':checked').length) {
                    return false;
                }
            });
            $('input[id^="event_transport"]').click(function () {
                if (!$(this).is(':checked')) {
                    $(this).parent().next().find(':checkbox').prop('checked', false);
                }

            });

        });
    </script>
@endpush
