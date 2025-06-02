@php
    use App\Enum\OrderMarker;
@endphp
<ul class="mfw-actions">
    @if(
   (
        $data->marker == OrderMarker::NORMAL->value
        || $data->order_invoice_id
    ) || (in_array($data->type, \App\Enum\OrderType::deposits()) && $data->status == App\Enum\EventDepositStatus::BILLED->value)
    )
        <li class=""
            data-id="{{ $data->order_id }}">
            <a href="{{ route('pdf-printer', [
            'type' => 'invoice',
            'identifier' => $data->uuid,
         ]) }}?download" class="btn btn-sm btn-outline-blue">
                {{ __('front/order.download_invoice') }}
            </a>
        </li>
    @endif

    @if(in_array($data->type, \App\Enum\OrderType::deposits()) && $data->status == App\Enum\EventDepositStatus::PAID->value)
        <li class=""
            data-id="{{ $data->order_id }}">
            <a href="{{ route('pdf-printer', [
            'type' => 'receipt',
            'identifier' => $data->uuid,
         ]) }}?download" class="btn btn-sm btn-outline-blue">
                {{ __('front/order.download_receipt') }}
            </a>
        </li>
    @endif

    @if($data->type == 'refund')
        <li class=""
            data-id="{{ $data->order_id }}">
            <a href="{{ route('pdf-printer', [
            'type' => 'refundable',
            'identifier' => $data->uuid,
         ]) }}?download" class="btn btn-sm btn-outline-blue">
                {{ __('front/order.download_credit_note') }}
            </a>
        </li>
    @else
        <li class=""
            data-id="{{ $data->id }}">
            <a href="{{ route($editDetailsRouteName, [
                'event' => $data->event_id,
                'order' => $data->order_id,
             ]) }}" class="btn btn-sm btn-outline-blue">
                {{ __('front/order.detail') }}
            </a>
        </li>
    @endif
</ul>
