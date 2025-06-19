<template id="eventContactsSecondaryFilterDropdown">
    <div class="dropdown" id="eventContactsSecondaryFilterBtn">
        <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
           {{ $secondaryFilter ? \App\Enum\SecondaryEventContactFilter::translated($secondaryFilter) : 'Accès rapides' }}
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ $route }}">Voir tous les résultats</a></li>
            @foreach(\App\Enum\SecondaryEventContactFilter::keys() as $key)
                <li><a class="dropdown-item"
                       href="{{ $route . '?secondaryFilter='.$key }}">{{ \App\Enum\SecondaryEventContactFilter::translated($key) }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</template>
<style>
    #eventContactsSecondaryFilterBtn .dropdown-menu > li > a {
        padding: 5px;
        font-size: 14px;
    }
</style>

@push('js')

    <script>

        function eventContactsSecondaryFilterInit() {

            setTimeout(function () {

                let lastCell = $('#event_contact-table_wrapper .row:first > div:last-of-type');
                console.log(lastCell, 'lastCell');
                lastCell.addClass('d-flex justify-content-end');
                lastCell.prepend($('#eventContactsSecondaryFilterDropdown').html());


            }, 1500);
        }

        eventContactsSecondaryFilterInit();
    </script>
@endpush
