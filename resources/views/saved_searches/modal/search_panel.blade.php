@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {!! csscrush_tag(public_path('css/multisearch.css')) !!}
@endpush
<div class="modal fade"
     id="modal_search_panel"
     tabindex="-1"
     aria-labelledby="the_search_panel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="modal-search-panel-ajax-form" class="modal-form" data-ajax="{{route('ajax')}}">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="the_search_panel">Rechercher</h1>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div id="search-builder" data-event-id="{{ $event_id ?? null }}"></div>
                </div>

                <div class="messages m-3"></div>

                <div class="modal-footer justify-content-start">
                    <button type="button"
                            class="btn btn-primary submit-btn"
                            data-bs-dismiss="modal"
                    >Rechercher
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('callbacks')
    <script>
        const getSavedSearchFilters = function () {
            return {!! $searchFiltersProvider->serveAsJsObject() !!};
        };
        // Define operators for filter types
        const string_operators = ['equal', 'not_equal', 'begins_with', 'not_begins_with', 'contains', 'not_contains', 'ends_with', 'not_ends_with', 'is_empty', 'is_not_empty', 'is_null', 'is_not_null'];
        const int_operators = ['equal', 'not_equal', 'is_null', 'is_not_null'];
        const select_operators = ['equal', 'not_equal', 'is_null', 'is_not_null'];
        const non_nullable_select_operators = ['equal', 'not_equal'];
        const date_operators = ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'is_null', 'is_not_null'];
        const boolean_operators = ['equal', 'not_equal'];

        // Helper function used for custom inputs
        function swapPlaceholderWithName(template, placeholder, newName) {
            let regex = new RegExp(placeholder, 'g');
            return template.replace(regex, newName);
        }

        // Set search type from server
        const searchType = '{{$searchType}}';

        // Set initial search filters if provided
        @if($searchFilters)
        const initialSearchFilters = {!! $searchFilters !!};
        @else
        const initialSearchFilters = null;
        @endif
    </script>

    <!-- Include custom search module -->
    <script src="{{ asset('js/custom-search-module.js') }}"></script>

    <!-- Required libraries -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
    <script src="{!! asset('js/utils.js') !!}"></script>
@endpush
