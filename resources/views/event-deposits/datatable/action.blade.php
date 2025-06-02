<div class="d-flex justify-content-end gap-2 list-unstyled">
    @if ($data->status == \App\Enum\EventDepositStatus::UNPAID->value)
        <span data-bs-toggle="modal" data-bs-target="#paymentcall_{{ $data->payment_call_id }}">
        <a href="#" class="btn btn-sm btn-secondary"
           data-bs-placement="top" data-bs-title="Envoyer un email"
           data-bs-toggle="tooltip">Demande de paiement</a>
    </span>

        <x-mfw::modal :route="route('ajax')"
                      title="Envoi d'une demande de paiement"
                      class="paymentCallModal"
                      :params="['payment_call_id' => $data->payment_call_id]"
                      question="Un email demandant le paiement de la caution d'un montant de {{ \MetaFramework\Accessors\Prices::readableFormat(($data->total_ttc)/100, showDecimals: false) }} sera envoyéà à <b>{{ $data->beneficiary_name }}</b>"
                      reference="paymentcall_{{ $data->payment_call_id }}"/>

        <a target="_blank"
           href="{{ route('custompayment.form', ['uuid' => Crypt::encryptString($data->payment_call_id)]) }}"
           class="btn btn-secondary btn-sm">
            Page de paiement
        </a>
    @else

        @if(!$data->has_invoice)
            @if($data->reimbursed_at)
                Remboursé le {{\Carbon\Carbon::parse($data->reimbursed_at)->format('d/m/Y \à H\hi')}}
            @else
                @if($data->total_ttc > 0)
                    <a href="#" data-id="{{$data->id}}"
                       class="action-reimburse btn btn-sm btn-primary d-flex align-items-center gap-2">
                        Rembourser
                        <x-buttons.spinner/>

                    </a>
                    @if ($data->status == \App\Enum\EventDepositStatus::PAID->value)
                        <x-buttons.invoiceable-link type="receipt"
                                                    :identifier="$data->uuid"
                                                    btnClass="btn-danger"
                                                    title="Reçu" icon="r"
                        />
                    @endif
                @endif
            @endif
        @endif

        @if(!$data->reimbursed_at)
            @if($data->has_invoice)
                @if($data->status == \App\Enum\EventDepositStatus::BILLED->value)
                    <x-buttons.invoiceable-link type="invoice"
                                                :identifier="$data->uuid"
                                                btnClass="btn-success"
                                                title="Facture"
                                                icon="f"
                    />
                @endif
                @if($data->status == \App\Enum\EventDepositStatus::PAID->value && $data->total_ttc > 0)
                    <x-buttons.invoiceable-link type="receipt"
                                                :identifier="$data->uuid"
                                                btnClass="btn-danger"
                                                title="Reçu"
                                                icon="r"
                    />
                @endif
            @else
                <a href="#"
                   data-id="{{$data->id}}"
                   class="action-make-invoice btn btn-sm btn-yellow d-flex align-items-center gap-2">
                    Facturer
                    <x-buttons.spinner/>
                </a>
            @endif
        @endif
    @endif
</div>
