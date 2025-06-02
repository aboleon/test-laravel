@props([
    'event_id' => null,
    'searchType' => "notset",
    'searchFiltersProvider' => null,
    'ajaxContainerId' => "the-ajax-container",
    'var1' => null,
    'searchFilters' => null
    ])
@php
    use App\Accessors\SavedSearchesAccessor;
    use App\Enum\SavedSearches;

    $currentSearchId = session('savedSearch.'. $searchType .'.currentId');
    $currentSearchName = session('savedSearch.'. $searchType .'.currentName');
    $savedSearchesIdToName = SavedSearchesAccessor::getIdToNameArray($searchType);
@endphp

<div class="btn-group" role="group" aria-label="Search module buttons">
    <button type="button"
            class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#modal_search_panel">
        <i class="fas fa-search"></i>
        Rechercher
    </button>
    @if($searchFilters)
        <button id="current-search-btn-save"
                type="button"
                class="btn mfw-bg-red-dark"
                data-bs-toggle="modal"
                data-bs-target="#modal_search_save_dialog">
            @if($currentSearchId)
                Modifier
            @else
                Sauver
            @endif
        </button>
        <button type="button"
                class="current-search-btn-delete btn mfw-bg-red-dark">RAZ
        </button>
    @endif


    @if($savedSearchesIdToName)

        <div class="btn-group" role="group">
            <button class="btn mfw-bg-red-dark dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                Mes recherches
                @if($currentSearchId)
                    <span class="badge bg-danger">*</span>
                @endif
            </button>
            <ul class="dropdown-menu mfw-bg-red-dark-items dropdown-menu-end my-saved-search-items">
                @foreach($savedSearchesIdToName as $id => $name)
                    <li>
                        <div class="dropdown-item d-flex justify-content-between align-items-center
                                @if($id===$currentSearchId) pulseRed @endif
                                "
                             data-id="{{$id}}"
                             href="#">
                                    <span>
                                        {{$name}}
                                        @if($id===$currentSearchId)
                                            *
                                        @endif
                                    </span>
                            <a href="#">Supprimer</a>
                        </div>

                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

@include('saved_searches.modal.search_panel', ['event_id' => $event_id])
@include('saved_searches.modal.search_save_dialog')

@push('js')
    <script>
        $(document).ready(function () {
            let jAjaxContainer = $('#{{$ajaxContainerId}}');

            $('.my-saved-search-items').on('click', 'a,span', function (e) {
                e.preventDefault();
                let elType = $(this).prop('tagName').toLowerCase();

                const id = $(this).closest('.dropdown-item').data('id');
                let action = 'loadSavedSearch';
                if ('a' === elType) {
                    action = 'deleteSavedSearch';
                }
                ajax('action=' + action + '&type={{$searchType}}&id=' + id, jAjaxContainer, {
                    successHandler: function (r) {
                        if (r.ok) {
                            utils.reload();
                        }
                    },
                });
            });

            $('.current-search-btn-delete')
                .off('click')                    // clear any earlier click handlers
                .on('click', function (e) {
                    e.preventDefault();
                    ajax('action=deleteSavedSearch&type={{$searchType}}', jAjaxContainer, {
                        successHandler: r => r.ok && utils.reload()
                    });
                });

        });
    </script>
@endpush

@pushonce('js')
    <script src="{!! asset('js/utils.js') !!}"></script>
@endpushonce
