<div class="row">

    <div class="col-md-6">

        <div class="d-flex align-items-center mb-3">
            <div class="d-inline-block me-3 w-25">
                <x-mfw::select name="grant_country_selector"
                               :values="\App\Models\Establishment::distinct('country_code')->pluck('country_code','country_code')->map(fn($item) => \MetaFramework\Accessors\Countries::getCountryNameByCode($item))->toArray()"
                               defaultselecttext="Pays"/>
            </div>
            <div class="d-inline-block me-3 w-25">
                <select class="form-control form-select" id="grant_locality_selector">
                    <option>Ville</option>
                </select>
            </div>
            <button class="btn btn-sm btn-success" id="add-grant-establishments-btn" type="button">
                <i class="fas fa-plus" style="font-size: smaller"></i> Ajouter
            </button>
        </div>
        <div id="grant-establishment-messages" data-ajax="{{ route('ajax') }}"></div>

        @php
            $grantEstablishments = $data->establishmentsWithCountry;
        @endphp
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Établissement</th>
                <th>Pays</th>
                <th>Ville</th>
            </tr>
            </thead>
            <tbody id="grant-establishments"></tbody>
        </table>
        <x-mfw::alert type="warning" class="eligible-establishments-alert d-none"
                      message="Aucun établissement disponible"/>
    </div>

    <div class="col-md-6">
        <table class="table">
            <thead>
            <tr>
                <th>Établissement</th>
                <th>Pays</th>
                <th>Ville</th>
                <th>Nombre PEC</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="grant-establishments-validated">
            @if ($errors->any() && old('grant_establishment'))
                @php
                    $establishments = \App\Models\Establishment::whereIn('id',old('grant_establishment.establishment_id'))->with('country')->get();

                @endphp
                @foreach($establishments as $establishment)
                    @include('events.manager.grant.establishment-row', [
                        'establishment'=>$establishment,
                        'random' => Str::random(14),
                        'establishment_id' => $establishment->id,
                        'pax' => old('grant_establishment.'.$loop->index.'.pax')
                    ])
                @endforeach

            @else
                @foreach($grantEstablishments as $grantEstablishment)
                    @php
                        $establishment = $grantEstablishment->establishment;
                    @endphp
                    @include('events.manager.grant.establishment-row', [
                        'establishment'=>$establishment,
                        'random' => Str::random(14),
                        'establishment_id' => $grantEstablishment->establishment_id,
                        'pax' => $grantEstablishment->pax
                    ])
                @endforeach
            @endif

            </tbody>
        </table>
        <div id="grant_binded_estanlishment_messages" data-ajax="{{ route('ajax') }}"></div>
    </div>
</div>
<template id="grant-establishment-delete">
    <x-mfw::simple-modal id="delete_grant_binded_establishment"
                         class="btn btn-danger btn-sm mt-2"
                         title="Suppression d'un établissement Grant"
                         confirmclass="btn-danger"
                         confirm="Supprimer"
                         callback="deleteGrantEstablishment"
                         text="Supprimer"/>
</template>


@push('callbacks')
    <script src="{{ asset('js/eventmanager/grant_establishment_callbacks.js') }}"></script>
@endpush
@push('js')
    <script src="{{ asset('js/eventmanager/grant_establishments.js') }}"></script>
@endpush
