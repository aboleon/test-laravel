<x-backend-layout>
    @php
        $error = $errors->any();
    @endphp

    <x-slot name="header">
        <h2>
            {{ trans_choice('ui.hotels.label',2) }}
        </h2>

        <div class="d-flex align-items-center gap-1" id="topbar-actions" x-data>
            @if ($data->id)
                <x-event-associator type="hotel" :id="$data->id" />
            @endif
            <x-back.topbar.separator />
            <x-back.topbar.edit-combo
                    route-prefix="panel.hotels"
                    item-name="l'hébergement {{ $data->name }}"
                    :model="$data"
                    :wrap="false"
            />
        </div>
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">
        <x-mfw::response-messages />
        <x-mfw::validation-errors />

        <h2 class="legend">{!! $data->name ?? trans_choice('ui.hotels.label',1) !!}</h2>
        <nav class="nav nav-tabs mb-4" id="nav-tab" role="tablist">
            <x-mfw::tab tag="hotel-tabpane" label="Fiche" :active="true" />
            <x-mfw::tab tag="history-tabpane" label="Historique d'affectation" />
        </nav>
        <div class="tab-content mt-3" id="nav-tabContent">
            <div class="tab-pane fade show active"
                 id="hotel-tabpane"
                 role="tabpanel"
                 aria-labelledby="hotel-tabpane-tab">

                <div class="row gx-5">
                    <div class="col-md-4 order-last wg-card">
                        @if(!$data->id)
                            <h4 class="m-0">Hôtels existants</h4>
                            <div class="position-relative"
                                 id="hotel-messages"
                                 data-ajax="{{ route('ajax') }}"></div>
                        @endif
                    </div>
                    <div class="col-md-8">

                        <form method="post"
                              action="{{ $data->id ? route('panel.hotels.update', $data->id) : route('panel.hotels.store') }}"
                              id="wagaia-form">
                            @csrf
                            @if($data->id)
                                @method('put')
                            @endif

                            @if (request()->filled('post_action'))
                                <input type="hidden"
                                       name="post_action"
                                       value="{{ request('post_action') }}" />
                            @endif
                            @if (request()->filled('event_id'))
                                <input type="hidden"
                                       name="event_id"
                                       value="{{ request('event_id') }}" />
                            @endif

                            <input type="hidden"
                                   name="custom_redirect"
                                   value="{!! request('save_and_redirect') !!}">

                            <div class="row mb-4">
                                <div class="col-lg-6 mb-3 position relative"
                                     id="hotel_search{{ $data->id ? '_disabled' : ''}}">
                                    <x-mfw::input name="hotel.name"
                                                  :label="__('account.last_name') . ' *'"
                                                  :value="$error ? old('hotel.name') : $data->name" />
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <x-mfw::select :values="$stars"
                                                   name="hotel.stars"
                                                   label="{{__('forms.fields.stars')}}"
                                                   :affected="$error ? old('hotel.stars') : $data->stars"
                                                   :nullable="true"
                                                   defaultselecttext="{{__('ui.hotels.no_ranking')}}" />
                                </div>
                            </div>


                            @foreach($data->mediaSettings() as $media)
                                <x-mediaclass::uploadable :model="$data" :settings="$media" />
                            @endforeach

                            <div class="row mt-5">
                                <div class="col-lg-12 mb-3">
                                    <h4>{{__('ui.hotels.address')}}</h4>
                                    <label class="form-label">{{ __('ui.hotels.address') . ' *' }}</label>
                                    <x-mfw::google-places :geo="$data?->address ?? new \App\Models\HotelAddress"
                                                          field="wa_geo" />
                                </div>
                            </div>

                            <x-mfw::translatable-tabs datakey="hotel"
                                                      :fillables="$data->fillables"
                                                      :model="$data" />

                            <div class="row mb-4">
                                <div class="col-lg-12 mb-3">
                                    <h4>{{__('ui.hotels.services')}}</h4>
                                    <div class="col-lg-12 mb-3 d-flex align-align-items-center gap-3">
                                        @forelse($services as $values)
                                            <x-mfw::checkbox name="hotel.services."
                                                             :value="$values->id"
                                                             :label="$values->name"
                                                             :affected="collect($data->services)" />
                                        @empty
                                            {{__('ui.hotels.no_services')}}
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-12 mb-3">
                                    <h4>{{__('ui.hotels.contacts')}}</h4>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <x-mfw::input name="hotel.first_name"
                                                  :label="__('account.first_name')"
                                                  :value="$error ? old('hotel.first_name') : $data->first_name" />
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <x-mfw::input name="hotel.last_name"
                                                  :label="__('account.last_name')"
                                                  :value="$error ? old('hotel.last_name') : $data->last_name" />
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <x-mfw::input name="hotel.phone"
                                                  :label="__('account.phone')"
                                                  :value="$error ? old('hotel.phone') : $data->phone" />
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <x-mfw::input name="hotel.email"
                                                  type="email"
                                                  :label="__('ui.email_address')"
                                                  :value="$error ? old('hotel.email') : $data->email" />
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <x-mfw::input name="hotel.website"
                                                  label="Site Internet"
                                                  :value="$error ? old('hotel.website') : $data->website" />
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="tab-pane fade"
                 id="history-tabpane"
                 role="tabpanel"
                 aria-labelledby="history-tabpane-tab">
                <table class="table table-hover yajra-datatable w-100">
                    <thead>
                    <tr>
                        <th>Évènement</th>
                        <th>Début</th>
                        <th>Fin</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                @include('lib.datatable')
                @push('js')
                    <script type="text/javascript">
                      $(function() {

                        $('.yajra-datatable').DataTable({
                          processing: true,
                          serverSide: true,
                          pageLength: 20,
                          language: {
                            url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json',
                          },
                          ajax: "{!! route('panel.hotels.history.datatable',['hotel_id' => $data->id]) !!}",
                          columns: [
                            {
                              data: 'event',
                              name: 'event',
                            },
                            {
                              data: 'event_starts',
                              name: 'event_starts',
                            },
                            {
                              data: 'event_ends',
                              name: 'event_ends',
                            },
                          ],
                        });

                      });
                    </script>
                @endpush
            </div>
        </div>
    </div>
    @push('css')
        {!! csscrush_tag(public_path('css/hotels.css')) !!}
    @endpush
    @pushonce('css')
        {!! csscrush_inline(public_path('vendor/mfw/css/fragments/_dynamic_search.css')) !!}
    @endpushonce

    @push('callbacks')
        <script>
          function eventHotelSearchResults(result) {
            let list = '<div class="suggestions"><ul>',
              i = 0;
            if (result.items.length) {
              for (i = 0; i < result.items.length; ++i) {
                list = list.concat('<li data-id="' + result.items[i].id + '"><a class="text-decoration-none" href="/panel/hotels/' + result.items[i].id + '/edit">' + result.items[i].name + ', ' + result.items[i].locality + ', ' + (result.items[i].country ?? 'NC') + '</a></li>');
              }
            } else {
              list = list.concat('<li data-id="none">Aucun résultat</li>');
            }
            list = list.concat('</ul></div>');
            $('#hotel-messages').html(list).find('.suggestions').show();
          }
        </script>
    @endpush

    @push('js')

        <script>

          activateEventManagerLeftMenuItem('hotels');

          function HotelSearch() {
            let DTC = $('#hotel_search'),
              DTC_Search = DTC.find(':text');

            DTC_Search.keyup(function() {
              let data = $(this).val();
              DTC.find('.suggestions').remove();
              setDelay(function() {
                if (data.length > 2) {
                  let formData = 'action=hotelSearch&callback=eventHotelSearchResults&keyword=' + data;
                  ajax(formData, $('#hotel-messages'));
                } else {
                  $('.suggestions').empty();
                }
              }, 500);
            });

          }

          HotelSearch();
        </script>
    @endpush

</x-backend-layout>
