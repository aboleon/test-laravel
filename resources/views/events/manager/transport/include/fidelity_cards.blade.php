<!-- cartes de fidélité et pièces identité -->
<div class="row mb-5 mt-5 tr-base">
    @php
        $cards = $account?->cards ?? [];
        $documents  = $account?->documents ?? [];
        $currentUrl = url()->current();
    @endphp
    <div class="col-md-6 mb-3">
        <h4>{{ trans_choice('account.fidelity_card', 2) }}</h4>
        <div class="row">
            @forelse($cards as $item)
                <div class="address-block mb-4 ps-0">
                    <div class="card">
                        <div class="card-body">
                            {!! \App\Printers\Account::card($item) !!}
                            <div class="mt-3 d-flex justify-content-between">
                                <a class="fw-bold link-dark"
                                   href="{{ route('panel.accounts.cards.edit', [
                                                               'account' => $account,
                                                               'card' => $item,
                                                               'redirect_to' => $currentUrl,
                                                                ]) }}">{{__('ui.modify')}}</a>
                                <a class="fw-bold link-danger"
                                   data-bs-toggle="modal"
                                   data-bs-target="#destroy_card{{ $item->id }}"
                                   href="#">{{ __('ui.delete') }}</a>
                            </div>
                            @push('modals')
                                <x-mfw::modal
                                    :route="route('panel.accounts.cards.destroy', [$account, $item])"
                                    reference="destroy_card{{ $item->id }}"/>
                            @endpush
                        </div>
                    </div>
                </div>
            @empty
                <x-mfw::alert type="warning" :message="__('account.no_fidelity_card_entered')"/>
            @endforelse
        </div>
        @if($account)
            <a href="{{route('panel.accounts.cards.create', [
                                        'account' => $account,
                                        'redirect_to' => $currentUrl,
                                        ])}}"
               class="btn btn-sm rounded-1 btn-secondary">{{__('account.add_new_fidelity_card')}}</a>
        @endif
    </div>

    <div class="col-md-6">
        <h4>{{ trans_choice('account.idcard', 2) }}</h4>
        <div class="row">
            @forelse($documents as $item)
                <div class="address-block mb-4 ps-0">
                    <div class="card">
                        <div class="card-body">
                            {!! \App\Printers\Account::document($item) !!}
                            <div class="mt-3 d-flex justify-content-between">
                                <a class="fw-bold link-dark"
                                   href="{{ route('panel.accounts.documents.edit', [
                                                                'account' => $account,
                                                                'document' => $item,
                                                                'redirect_to' => $currentUrl,
                                                                ])}}">{{__('ui.modify')}}</a>
                                <a class="fw-bold link-danger"
                                   data-bs-toggle="modal"
                                   data-bs-target="#destroy_document{{ $item->id }}"
                                   href="#">{{ __('ui.delete') }}</a>
                            </div>
                            @push('modals')
                                <x-mfw::modal
                                    :route="route('panel.accounts.documents.destroy', [$account, $item])"
                                    reference="destroy_document{{ $item->id }}"/>
                            @endpush
                        </div>
                    </div>
                </div>
            @empty
                <x-mfw::alert type="warning" :message="__('account.no_id_card_entered')"/>
            @endforelse
        </div>
        @if($account)
            <a href="{{route('panel.accounts.documents.create', [
                                        'account' => $account,
                                        'redirect_to' => $currentUrl,
                                        ])}}"
               class="btn btn-sm rounded-1 btn-secondary">{{__('account.add_new_id_card')}}</a>
        @endif
    </div>

</div>

<div class="row mb-3 tr-divine">
    <x-mfw::textarea label="{{__('transport.travel_preferences')}}"
                     height="100"
                     name="item[main][travel_preferences]"
                     :value="$error ? old('item.main.travel_preferences') : $transport?->travel_preferences"/>
</div>
