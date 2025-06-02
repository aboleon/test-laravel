<x-event-manager-layout :event="$event">

    <x-slot name="header">
        <h2 class="event-h2">

            <span>{{ $accommodation->hotel->name }}</span> &raquo;
            <span>Récap Groupes</span>
        </h2>
        <x-back.topbar.edit-combo
            :event="$event"
            :index-route="route('panel.manager.event.accommodation.index', $event)"
            :use-create-route="false"
        />
    </x-slot>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <x-mfw::validation-banner/>
        <x-mfw::response-messages/>

        @include('events.manager.accommodation.tabs')
        <div class="row mt-4 mb-3">
            <div class="col-lg-4 col-sm-6">
                <table class="table">
                    <tr>
                        <th>Total résas utilisées</th>
                        <td id="group-total-booked"></td>
                    </tr>
                    <tr>
                        <th>Total résas bloquées</th>
                        <td id="group-total-blocked"></td>
                    </tr>
                    <tr>
                        <th>Total résas restant</th>
                        <td id="group-total-delta">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-8 col-sm-6">
                <p class="fw-bold d-block text-danger mb-3">Groupes ayant un contingent bloqué uniquement</p>
                @if ($groups)
                    <x-mfw::select :values="$groups"
                                   label="Groupe" name="group"/>
                @else
                    <x-mfw::notice message="Aucun groupe avec chambres bloquées"/>
                @endif
            </div>
        </div>
        <div id="messages" data-ajax="{{ route('ajax') }}"></div>
        <div class="position-relative" id="group-container" data-event-accommodation-id="{{ $accommodation->id }}">
        </div>
    </div>
    @if ($groups)
        @push('callbacks')
            <script>
                function appendGroupRecapView(result) {
                    let c = $('#group-container');
                    c.html(result.html);

                    let recap = c.find('tfoot');
                    $('#group-total-booked').text(recap.attr('data-booked'));
                    $('#group-total-blocked').text(recap.attr('data-blocked'));
                    $('#group-total-delta').text(recap.attr('data-delta'));

                }
            </script>
        @endpush
        @push('js')
            <script>
                ajax('action=fetchEventAccommodationRecapForGroup&callback=appendGroupRecapView&event_accommodation_id=' + $('#group-container').attr('data-event-accommodation-id') + '&group_id=0', $('#messages'));

                $(function () {

                    $('select#group').change(function () {
                        let value = $(this).val(), c = $('#group-container');

                        setVeil(c);
                        ajax('action=fetchEventAccommodationRecapForGroup&callback=appendGroupRecapView&event_accommodation_id=' + c.attr('data-event-accommodation-id') + '&group_id=' + value, $('#messages'));
                    });
                });
            </script>
        @endpush
    @endif
</x-event-manager-layout>
