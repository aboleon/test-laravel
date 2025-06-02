@php
    $error = $errors->any();
@endphp

@pushonce('css')
    {!! csscrush_inline(public_path('vendor/mfw/css/fragments/_dynamic_search.css')) !!}
@endpushonce

<div class="row mb-4 gx-5 form" data-ajax="{{route('ajax')}}">
    <div class="col-md-4 order-last wg-card">
        @if(!$data->id)
            <h4 class="m-0">Lieux existants</h4>
            <div class="position-relative" id="place-messages" data-ajax="{{ route('ajax') }}"></div>
        @endif
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="col-12 mb-3 position relative" id="place_search{{ $data->id ? '_disabled' : ''}}">
                <x-mfw::input name="place.name"
                              :label="__('account.last_name') . ' *'"
                              :value="$error ? old('place.name') : $data->name"/>
            </div>
            <div class="col-lg-6 mb-3 pt-3 mfw-holder position-relative">
                <div class="d-flex justify-content-between align-items-end">
                    <div class="w-100 me-3">
                        <x-selectable-dictionnary key="place_types"
                                                  name="place[place_type_id]"
                                                  :affected="$error ? old('place.place_type_id') : $data->place_type_id"/>
                    </div>
                    <span class="fs-4 add-dynamic dict-dynamic"
                          data-dict="place_types"><i
                            class="fa-solid fa-circle-plus"></i></span>
                </div>
            </div>
            <div class="col-lg-6 mb-3 pt-3">
                <x-mfw::input type="email"
                              name="place[email]"
                              :label="__('forms.fields.email')"
                              :value="$error ? old('place.email') : $data->email"/>
            </div>
            <div class="col-lg-6 mb-3 pt-3">
                <x-mfw::input name="place[phone]"
                              :label="__('forms.fields.phone')"
                              :value="$error ? old('place.phone') : $data->phone"/>
            </div>
            <div class="col-lg-6 mb-3 pt-3">
                <x-mfw::input name="place[website]"
                              :label="__('forms.fields.website')"
                              :value="$error ? old('place.website') : $data->website"/>
            </div>
            <div class="col-12">
                @if ($data->mediaSettings())
                    @foreach($data->mediaSettings() as $media)
                        <x-mediaclass::uploadable :description="false" :model="$data" :settings="$media"/>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-lg-12 mb-3">
        <h4>{{__('ui.hotels.address')}}</h4>
        <label class="form-label">{{ __('ui.hotels.address') . ' *' }}</label>
        <x-mfw::google-places :geo="$data?->address ?? new \App\Models\PlaceAddress"
                              :params="['types' => ['geocode','establishment']]"
                              field="wa_geo"/>
    </div>
</div>

<x-mfw::translatable-tabs :fillables="$data->fillables" datakey="place" :model="$data"/>

<x-optional-hidden string="selectable"/>

@include('accounts.shared.dict_template')
@push('modals')
    @include('mfw-modals.launcher')
@endpush
@push('callbacks')
    <script>
        function eventPlaceSearchResults(result) {
            let list = '<div class="suggestions"><ul>',
                i = 0;
            if (result.items.length) {
                for (i = 0; i < result.items.length; ++i) {
                    list = list.concat('<li data-id="' + result.items[i].id + '"><a class="text-decoration-none" href="/panel/places/' + result.items[i].id + '/edit">' + result.items[i].name + ', ' + result.items[i].locality + ', ' + (result.items[i].country ?? 'NC') + '</a></li>');
                }
            } else {
                list = list.concat('<li data-id="none">Aucun résultat</li>');
            }
            list = list.concat('</ul></div>');
            $('#place-messages').html(list).find('.suggestions').show();
        }
    </script>
@endpush
@pushonce('js')
    <script>
        function PlaceSearch() {
            let DTC = $('#place_search'),
                DTC_Search = DTC.find(':text');

            DTC_Search.keyup(function () {
                let data = $(this).val();
                DTC.find('.suggestions').remove();
                setDelay(function () {
                    if (data.length > 2) {
                        let formData = 'action=placeSearch&callback=eventPlaceSearchResults&keyword=' + data;
                        ajax(formData, $('#place-messages'));
                    } else {
                        $('.suggestions').empty();
                    }
                }, 500);
            });
        }
        PlaceSearch();
    </script>
    <script src="{!! asset('js/dynamic_dictionnary.js') !!}"></script>
@endpushonce
