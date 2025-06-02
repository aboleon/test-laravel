@php use App\Accessors\ParticipationTypes;use App\Enum\ParticipantType; @endphp
<div class="modal fade"
     id="modal_add_accommodation_panel"
     tabindex="-1"
     aria-labelledby="modal_add_accommodation_panel_label"
     aria-hidden="true">
    <div class="modal-dialog big-select2-context">
        <form class="modal-form" data-ajax="{{route('ajax')}}">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-end">
                    <h1 class="modal-title fs-5" id="modal_add_accommodation_panel_label">
                        Ajouter / Créer</h1>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">

                    <div class="alert alert-danger select-hotel-error"
                         style="display: none">
                        Veuillez sélectionner un hôtel.
                        <br> Si vous ne trouvez pas votre hôtel, cliquez sur Créer.
                    </div>


                    <input type="hidden" name="event_id" , value="{{$event->id}}">

                    <div class="mt-4">
                        <select
                            name="hotel_id"
                            id="hotel_id_select">
                        </select>
                    </div>


                </div>

                <div class="messages m-3"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('ui.close') }}
                    </button>
                    <button type="button" class="btn btn-success btn-submit">Créer</button>
                    <button type="button" class="btn btn-default btn-add-hotel">Ajouter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>




@pushonce('js')
    <script src="{!! asset('js/select2AjaxWrapper.js') !!}"></script>
@endpushonce
@include('lib.select2')

@push('js')
    <script>

        $(document).ready(function () {

            const jModal = $('#modal_add_accommodation_panel');
            const jForm = jModal.find('form');
            const jHotelSelect = jForm.find('#hotel_id_select');

            $('#hotel_id_select').select2AjaxWrapper({
                placeholder: 'Sélectionner un hôtel',
                language: 'fr',
                // necessary to allow input focus inside a modal
                dropdownParent: jModal,
                ajax: {
                    url: "{{ route('ajax') }}",
                    delay: 100,
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term,
                            action: 'select2Hotels',
                            event_id: {{$event->id}},
                        };
                    },
                },
            });

            $('#hotel_id_select').on('select2:select', function (e) {
                jModal.find('.select-hotel-error').hide();
            });

            jForm.find('.btn-add-hotel').on('click', function () {
                let groupId = jHotelSelect.val();
                if (!groupId) {
                    jModal.find('.select-hotel-error').show();
                } else {
                    let formData = jForm.serialize();
                    ajax('action=eventHotelAssociate&' + formData + '&event_id={{$event->id}}', jForm, {
                        successHandler: function () {
                            redrawDataTable();
                            return true;
                        },
                    });
                }

                return false;
            });

            jForm.find('.btn-submit').on('click', function () {
                window.location.href = "{!! route('panel.hotels.create',['post_action' => 'event_hotel_association', 'event_id' => $event->id]) !!}";
                return false;
            });
        });

    </script>
@endpush
