@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade {{ !$data->id ? 'show active' : '' }}"
     id="config-tabpane"
     role="tabpanel"
     aria-labelledby="config-tabpane-tab">
    <div class="row gx-5">
        <div class="col-sm-6">
            <div class="d-flex justify-content-between">
                <h4>Intitulé</h4>
                <div>
                    <x-mfw::checkbox name="grant.active"
                                     :switch="true"
                                     label="Activé"
                                     value="1"
                                     :affected="$error ? old('grant.active') : ($data->id ? $data->active : 1)"/>
                </div>
            </div>
            <x-mfw::translatable-tabs datakey="grant"
                                      :fillables="$data->fillables"
                                      :pluck="['title']"
                                      :model="$data"/>

            <div class=" mfw-line-separator mb-4"></div>

            <h4>Paramètres</h4>
            <div class="row gy-3 mb-3" x-data="{ showType: '{{$data?->amount_type??'ht'}}' }">


                <div class="col-12">
                    <x-mfw::radio name="grant.amount_type" :values="['ht' => 'Montant HT', 'ttc' => 'Montant TTC']"
                                  :affected="$error ? old('grant.amount_type') : $data->amount_type"/>
                </div>


                <div class="col-sm-6">
                    <x-mfw::number name="grant.amount"
                                   label="Montant"
                                   :value="$error ? old('grant.amount') : $data->amount"/>
                </div>


                <div class="col-sm-4">
                    <x-mfw::number name="grant.pax_min"
                                   label="Nombre personnes min."
                                   :value="$error ? old('grant.pax_min') : $data->pax_min"/>
                </div>
                <div class="col-sm-4">
                    <x-mfw::number name="grant.pax_max"
                                   label="Nombre personnes max"
                                   :value="$error ? old('grant.pax_max') : $data->pax_max"/>
                </div>
                <div class="col-sm-4">
                    <x-mfw::number name="grant.pax_avg"
                                   label="Nombre personnes moyen"
                                   :value="$error ? old('grant.pax_avg') : $data->pax_avg"/>
                </div>
                <div class="col-sm-4">
                    <x-mfw::number name="grant.pec_fee"
                                   label="Frais de dossier PEC/Pax TTC"
                                   :value="old('grant.pec_fee', $data?->id?$data->pec_fee:$event->pec->processing_fees)"/>
                </div>
                <div class="col-sm-4">
                    <x-mfw::number name="grant.deposit_fee"
                                   label="Montant caution TTC *"
                                   :value="old('grant.deposit_fee', $data?->id?$data->deposit_fee:$event->pec->waiver_fees)"/>
                </div>
                <div class="col-sm-4">
                    <x-mfw::datepicker name="grant.prenotification_date"
                                       label="Date envoi liste préliminaire *"
                                       :value="old('grant.prenotification_date', $data?->id?$data->prenotification_date->format('d/m/Y'):null)"/>
                </div>
            </div>


            <x-mfw::translatable-tabs datakey="grant"
                                      :fillables="$data->fillables"
                                      :pluck="['comment']"
                                      :model="$data"/>


        </div>
        <div class="col-sm-6">
            <h4>Contact</h4>
            <div class="row gy-3 mb-5 mfw-line-separator pb-5">
                <div class="col-sm-6">
                    <x-mfw::input name="grant_contact.first_name"
                                  :label="__('account.first_name') . ' *'"
                                  :value="old('grant_contact.first_name', $data->contact?->first_name)"/>
                </div>
                <div class="col-sm-6">
                    <x-mfw::input name="grant_contact.last_name"
                                  :label="__('account.last_name') . ' *'"
                                  :value="old('grant_contact.last_name', $data->contact?->last_name)"/>
                </div>
                <div class="col-sm-6">
                    <x-mfw::input type="email"
                                  name="grant_contact.email"
                                  :label="__('mfw.email_address')  . ' *'"
                                  :value="old('grant_contact.email', $data->contact?->email)"/>
                </div>
                <div class="col-sm-6">
                    <x-mfw::input name="grant_contact.phone"
                                  :label="__('account.phone') . ' *'"
                                  :value="old('grant_contact.phone', $data->contact?->phone)"/>
                </div>
                <div class="col-sm-6">
                    <x-mfw::input name="grant_contact.fonction"
                                  label="Fonction"
                                  :value="old('grant_contact.fonction', $data->contact?->fonction)"/>
                </div>
                <div class="col-sm-6">
                    <x-mfw::input name="grant_contact.service"
                                  label="Service"
                                  :value="old('grant_contact.service', $data->contact?->service)"/>
                </div>
            </div>


            <h4>Transport</h4>
            @php
            $refundTransportIsChecked = $error ? (bool)old('grant.refund_transport') : ($data?->id ? $data->refund_transport : true);
            @endphp
            <div class="row gy-3 mb-5 pb-5" id="transport_management_tab">
                <div class="col-sm-6 manage_transfert_upfront">
                    <x-mfw::checkbox name="grant.refund_transport"
                                     :switch="true"
                                     label="Remboursement transport"
                                     value="1"
                                     :affected="$refundTransportIsChecked"/>
                </div>
                <div
                    class="col-sm-6 grant_refund_transport {{ $refundTransportIsChecked ? '' : 'd-none' }}">
                    <x-mfw::number name="grant.refund_transport_amount"
                                   label="Montant max du remboursement"
                                   :value="old('grant.refund_transport_amount', $data->refund_transport_amount)"/>
                    <small class="text-dark">* HT ou TTC, en fonction de la configuration GRANT</small>
                </div>
                <div
                    class="col-12 grant_refund_transport {{ $refundTransportIsChecked ? '' : 'd-none' }}">
                    <x-mfw::translatable-tabs datakey="grant"
                                              :fillables="$data->fillables"
                                              :pluck="['refund_transport_text']"
                                              :model="$data"/>
                </div>
            </div>


        </div>
    </div>
</div>
@push('js')
    <script>
        let mtu = $('.manage_transfert_upfront'), gft = $('.grant_refund_transport'),
            ttab = $('#transport_management_tab');
        $('input[name="grant[manage_transport_upfront]"]').off().click(function () {
            if ($(this).is(':checked')) {
                mtu.removeClass('d-none');
            } else {
                ttab.find(':checkbox').prop('checked', false);
                mtu.addClass('d-none');
                gft.addClass('d-none');
            }
        });
        $('input[name="grant[refund_transport]"]').off().click(function () {
            $(this).is(':checked') ? gft.removeClass('d-none') : gft.addClass('d-none');
        });
    </script>
@endpush
