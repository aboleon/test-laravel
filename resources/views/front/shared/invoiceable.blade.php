@if($invoiceable)
    <h5>{{ __('front/order.payer_info') }}</h5>
    <table class="table table-sm table-bordered">
        @if($invoiceable->company)
            <tr>
                <th>{{ __('front/order.company') }}</th>
                <td>{{ $invoiceable->company }}</td>
            </tr>
        @endif
        <tr>
            <th>{{ __('account.last_name') }}</th>
            <td>
                @if ($invoiceable->account_type == \App\Enum\OrderClientType::GROUP->value)
                {{ $invoiceable->account->names() }} |
                @endif
                {{ $invoiceable->last_name }} {{ $invoiceable->first_name }}
            </td>
        </tr>
        @if($invoiceable->department)
            <tr>
                <th>{{ __('front/order.service') }}</th>
                <td>{{ $invoiceable->department }}</td>
            </tr>
        @endif
        @if($invoiceable->vat_number)
            <tr>
                <th>{{ __('front/order.vat_number') }}</th>
                <td>{{ $invoiceable->vat_number }}</td>
            </tr>
        @endif
        @if(trim(str_replace('null','',$invoiceable->text_address)))
            <tr>
                <th>{{ trans_choice('ui.address',1) }}</th>
                <td>{{ $invoiceable->text_address }}</td>
            </tr>
        @endif
        @if($invoiceable->cedex)
            <tr>
                <th>Cedex</th>
                <td>{{ $invoiceable->cedex }}</td>
            </tr>
        @endif
        @if(trim(str_replace('null','',$invoiceable->complementary)))
            <tr>
                <th>{{ trans_choice('front/order.address_complementary',1) }}</th>
                <td>{{ $invoiceable->complementary }}</td>
            </tr>
        @endif

    </table>
@endif
