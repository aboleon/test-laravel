<ul class="mfw-actions">
    <x-mfw::edit-link
        :route="route('panel.manager.event.orders.edit', ['event' => $data->event_id, 'order' => $data->id])"/>
    @if($orderAccessor->hasEmptyContent())
        <x-mfw::delete-modal-link reference="{{ $data->id }}"/>
    @endif

    @if(!$orderAccessor->ispaid())
        <li data-bs-toggle="modal" data-bs-target="#resend_by_mail_{{ $data->id }}">
            <a href="#" class="mfw-edit-link btn btn-sm btn-warning"
               data-bs-placement="top" data-bs-title="Relance par e-mail"
               data-bs-toggle="tooltip"><i class="fas fa-envelope"></i></a>
        </li>
    @endif

    @if ($data->invoices->isNotEmpty())
        @foreach($data->invoices->sortBy('proforma') as $invoice)
            <li>
                <a href="{{ route('pdf-printer', ['type' => 'invoice', 'identifier' => $data->uuid]) . ($invoice->proforma ? '?proforma='.$invoice->id : '')  }}"
                   class="mfw-edit-link btn btn-sm btn-{{ $invoice->proforma ? 'warning' : 'success' }}" target="_blank"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top" data-bs-title="Facture {{ $invoice->proforma ? 'Proforma' : '' }}">
                    <i class="fa-solid fa-f"></i>
                    @if ($invoice->proforma)
                        <i class="fa-solid fa-p"></i>
                    @endif
                </a>
            </li>
        @endforeach
    @endif

    @if ($data->client_type == \App\Enum\OrderClientType::ORATOR->value)
        <li>
            <a href="{{ route('pdf-printer', ['type' => 'invoice', 'identifier' => $data->uuid]) . '?proforma=proforma'  }}"
               class="mfw-edit-link btn btn-sm btn-warning" target="_blank"
               data-bs-toggle="tooltip"
               data-bs-placement="top" data-bs-title="Facture Proforma">
                <i class="fa-solid fa-f"></i>
                <i class="fa-solid fa-p"></i>
            </a>
        </li>
    @endif
    @if ($data->refunds->isNotEmpty())
        @foreach($data->refunds as $refund)
            <li>
                <x-buttons.invoiceable-link type="refundable"
                                            :identifier="$refund->uuid"
                                            btnClass="btn-danger"
                                            title="Avoir"
                                            icon="a"
                />
            </li>
        @endforeach
    @endif
</ul>
@if($orderAccessor->hasEmptyContent())
<x-mfw::modal :route="route('panel.manager.event.orders.destroy', ['event'=>$data->event_id, 'order' => $data->id])"
              title="Suppression d'une commande"
              question="Supprimer la commannde #{{ $data->id }} ?"
              reference="destroy_{{ $data->id }}"/>
@endif

@if(!$orderAccessor->ispaid())
    <x-mfw::modal :route="route('panel.mailer', ['type' => 'resendOrder', 'identifier' => $data->uuid])"
                  title="Relancer la commande par e-mail ?"
                  :params="['uuid' => $data->uuid]"
                  class="resendorderbymail"
                  question=""
                  reference="resend_by_mail_{{ $data->id }}"/>
@endif
