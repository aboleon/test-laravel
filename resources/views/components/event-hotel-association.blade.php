<style>
    #DT-event-hotel {
        display: flex;
        white-space: nowrap;
        align-items: center;
    }

    #DT-event-hotel label {
        margin: 0 8px 0;
    }
</style>
@pushonce('css')
    {!! csscrush_tag(public_path('vendor/mfw/css/fragments/_dynamic_search.css')) !!}
@endpushonce
<template id="template-dt-event-hotel">
    <div class="me-3 w-50 position-relative" id="DT-event-hotel">
        <x-mfw::input type="search" name="event_hotel_search" :params="['placeholder'=>'Tapez le nom d\'un hôtel, d\'une ville...','data-event_id' => $event->id]" label="Associer un hôtel : "/>
        @if(true === 'moved to topbar actions')
        <a class="btn btn-sm btn-success ms-2"
           href="{{ route('panel.hotels.create',['post_action' => 'event_hotel_association', 'event_id' => $event->id]) }}">
            <i class="fa-solid fa-circle-plus"></i> Créer</a>
        @endif
    </div>
</template>
<template id="template-dt-event-hotel-messages">
    <div class="row">
        <div id="DT-event-hotel-messages" class="col" data-ajax="{{ route('ajax') }}"></div>
    </div>
</template>
@push('callbacks')
    <script>

        function associateHotelToEvent() {
            let c = $('#DT-event-hotel .suggestions');
            c.find('li').click(function () {
                let event_id = $('#event_hotel_search').data('event_id'),
                    hotel_id = $(this).data('id');
                c.remove();
                if (hotel_id !== 'none') {
                    let formData = 'action=eventHotelAssociate&hotel_id=' + hotel_id + '&event_id=' + event_id;
                    ajax(formData, $('#DT-event-hotel-messages'));
                    DTEventHotelRedraw();
                }
            });

        }

        function eventHotelSearchResults(result) {
            let list = '<div class="suggestions"><ul>',
                i = 0;
            if (result.items.length) {
                for (i = 0; i < result.items.length; ++i) {
                    list = list.concat('<li data-id="' + result.items[i].id + '">' + result.items[i].name + ', ' + result.items[i].locality + '</li>');
                }
            } else {
                list = list.concat('<li data-id="none">Aucun résultat</li>');
            }
            list = list.concat('</ul></div>');
            $('#DT-event-hotel').append(list).find('.suggestions').show();
            associateHotelToEvent();
        }

        function DT_EventHotel_Manipulations() {
            let DTC = $('#DT-event-hotel'),
                DTC_Search = $('#event_hotel_search');

            DTC_Search.keyup(function () {
                let data = $(this).val(),
                    tag = $(this).data('type');
                DTC.find('.suggestions').remove();
                setDelay(function () {
                    if (data.length > 2) {
                        let formData = 'action=eventHotelSearch&callback=eventHotelSearchResults&keyword=' + data + '&event_id=' + DTC_Search.data('event_id');
                        ajax(formData, $('#DT-event-hotel-messages'));
                    } else {
                        $('.suggestions').empty();
                    }
                }, 500);
            });

        }

        function DTEventHotelRedraw() {
            $('.messages').not('#DT-event-hotel-messages .messages').html('');
            $('.dt').DataTable().ajax.reload();
        }
    </script>
@endpush

@push('js')

    <script>
        setTimeout(function () {
            let et = $('#eventmanager-accommodation-table_wrapper'),
                et_row_first = et.find('.row:first');
            et_row_first.find('> div:first-of-type').removeClass('col-md-6').addClass('col-md-2');
            et_row_first.find('> div:last-of-type').removeClass('col-md-6').addClass('d-flex justify-content-end col-md-10');
            $($('#template-dt-event-hotel').html()).insertBefore($('#eventmanager-accommodation-table_filter'));
            $($('#template-dt-event-hotel-messages').html()).insertBefore(et.find('.dt-row'));

            DT_EventHotel_Manipulations();

        }, 1000);
    </script>
@endpush
