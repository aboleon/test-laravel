<x-backend-layout>
    <x-slot name="header">
        <h2>
            Test disponibilité
        </h2>
    </x-slot>

    @php
        $error = $errors->any();
    @endphp

    <x-mfw::response-messages/>
    <x-mfw::validation-errors/>

    <div class="shadow p-4 bg-body-tertiary rounded">

        <form method="post" id="testable">
            @csrf

            <input type=hidden name="callback" value="showAvailability" />

            <div class="row">
                <div class="col-sm-6">
                    <x-mfw::select :values="$events" name="event" label="Évènement"/>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-6">
                    @include('testable.order_subjects')
                </div>
            </div>
            <div class="row my-3 mb-4">
                <div class="col-sm-6">
                    <x-mfw::datepicker name="entry_date" label="Entrée"/>
                </div>
                <div class="col-sm-6">
                    <x-mfw::datepicker name="out_date" label="Sortie"/>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6" id="hotels_container">
                    <x-mfw::select :values="$hotels" name="hotel" label="Hébergements"/>
                </div>
            </div>
        </form>
        <div id="messages" data-ajax="{{ route('ajax') }}"></div>
        <div id="recap"></div>

    </div>

    @push('js')
        <script>

            // Callbacks

            function showAvailability(result) {
                $('#recap').html('');
                if (!result.hasOwnProperty('error')) {
                    $('#recap').html(result.html);
                }
            }

            function setHotelsAndContacts(result) {
                let hotels = '<option value="">--- Sélectionner</option>',
                    contacts = hotels,
                    groups = contacts;

                if (Object.keys(result.hotels).length) {
                    for (const [key, value] of Object.entries(result.hotels)) {
                        hotels += `<option value="${key}">${value}</option>`;
                    }
                }
                if (Object.keys(result.subjects.contacts).length) {
                    for (const [key, value] of Object.entries(result.subjects.contacts)) {
                        contacts += `<option value="${key}">${value}</option>`;
                    }
                }
                if (Object.keys(result.subjects.groups).length) {
                    for (const [key, value] of Object.entries(result.subjects.groups)) {
                        groups += `<option value="${key}">${value}</option>`;
                    }
                }

                $('#hotel').html(hotels);
                $('#order_contact_id').html(contacts);
                $('#order_group_id').html(groups);
                setClientAccount();
                fetchAvailability();
            }

            function clearAll() {
                $('#recap, #account_info').html('');
                $('#participation_type').val('');
                $('#client-type-subselector select').each(function () {
                    $(this).find('option').not(':first').remove().change();
                });
                $('#hotel').find('option').not(':first').remove().change();
            }

            function setEvent() {
                $('#event').change(function () {
                    let val = $(this).val();
                    if (!val) {
                        clearAll();
                        return false;
                    }
                    ajax('action=testableAvailabilityHotels&event=' + val + '&callback=setHotelsAndContacts', $('#messages'));
                });
            }

            function fetchAvailability() {
                $('#hotel').off().change(function () {
                    let val = $(this).val();
                    if (!val) {
                        return false;
                    }
                    ajax('action=testableAvailabilityFetch&'+$('form#testable').find('input,select').serialize(), $('#messages'));
                });
            }

            // Main code

            setEvent();

            $('#client-type-selector :radio').click(function () {
                let client_type = $(this).val();
                $('#client-type-subselector > div').addClass('d-none');
                $('#client-type-subselector select, #hotel').find('option:first').prop('selected', true).change();
                $('#order_affectable_' + client_type).removeClass('d-none');
                $('#recap').html('');
            });

            function setClientAccount() {
                $('#client-type-subselector select').off().change(function () {
                    $('#account_info, #show-accommodation-recap, #accommodation-cart').html('');
                    $('#participation_type').val('');
                    updateGenericTotals();
                    if ($(this).val()) {
                        ajax('action=orderSelectAccountForAssignment&event_id=' + $('#event').val() + '&order_client_type=' + $('#client-type-selector :checked').val() + '&order_payer_id=' + $(this).val(), $('#messages'));
                    }
                });
            }

        </script>
    @endpush

</x-backend-layout>
