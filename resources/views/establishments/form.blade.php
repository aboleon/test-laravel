@php
    $error = $errors->any();
@endphp
<div class="row" id="establishment-form">
    {{--
    <!--    Demande de suppression de cette fonctionnalité le 13/06 par C.Alcareaz dans le Trello Bugs TUTO2
            Elle est pourtant utile pour ne pas créer des doublons
    -->
    <div class="col-12 mb-3">
        <div class="mfw-line-separator pb-4 position-relative" id="establishment_search_result" data-ajax="{{ route('ajax') }}">
            <x-mfw::input name="search_establishement" label="Rechercher un établissement déjà présent (recherche par nom)"/>
        </div>
    </div>
    --}}
    <div class="col-12">
        <x-mfw::radio name="establishment[type]" default="private" :values="$types" :affected="$errors->any() ? old('establishment.type') : $data->type" label="Type d'établissement"/>
    </div>

    <div class="col-12 mb-3">
        <x-mfw::input name="establishment[name]" :label="__('ui.title') . ' *'" :value="$error ? old('establishment.name') : $data->name"/>
    </div>
    <div class="col-12">
        <x-mfw::google-places field="establishment" :geo="$data ?? new \App\Models\Establishment" :params="['types' => ['establishment']]" label="Nom adresse *"/>
    </div>
    <div class="col-12">

        <x-mfw::textarea label="Complement d'adresse " height="100" name="establishment[prefix]" :value="$error ? old('wa_geo.prefix') : $data->prefix"/>
    </div>
</div>
