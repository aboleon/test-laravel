@php
    $error = $errors->any();
@endphp

    <div class="row pt-3">
        <div class="col-md-6 pe-sm-5">
            <div class="row mb-4 mfw-line-separator pb-4">
                <div class="col-md-5">
                    <div class="my-2">
                        <x-mfw::checkbox name="sellable_has_deposit" :switch="true" label="Cette prestation nécessite caution" value="1" :affected="$error ? old('sellable_has_deposit') : !is_null($data->deposit)"/>
                    </div>
                    <div class="sellable_deposit_is_grant {{ $error ? (old('sellable_has_deposit') ? '' : 'd-none') : (!is_null($data->deposit) ? '' : 'd-none') }}">
                        <x-mfw::checkbox name="sellable_deposit_is_grant" :switch="true" label="La caution concerne le GRANT" value="1" :affected="$error ? old('sellable_deposit_is_grant') : $data->deposit?->is_grant"/>
                    </div>
                </div>

                <div class="col-md-4">
                    <x-mfw::number name="sellable_deposit" label="Montant" :value="$error ? old('sellable.stock') : $data->stock"/>
                </div>
            </div>
            @include('events.manager.sellable.inc.grant-binded-location')
        </div>
        <div class="col-md-6 ps-sm-5">
            <div class="grant_deposit {{ $data->deposit?->is_grant ? '' : 'd-none' }}">
                <h4>Types de participations</h4>
                <div id="participation_types_grant">
                    <x-participation-types :filter="true" :subset="$event->participations->pluck('id')->toArray()" name="grant_participation_types" :affected="$error ? old('grant_participation_types') : $data->deposit?->grantBindedParticipations->pluck('id')"/>
                </div>
            </div>
        </div>
    </div>

@push('js')
    <script>
      $('#sellable_has_deposit').click(function() {
        $('.sellable_deposit_is_grant').toggleClass('d-none');
      });
      $('#sellable_deposit_is_grant').click(function() {
        $('.grant_deposit').toggleClass('d-none');
      });
    </script>
@endpush
