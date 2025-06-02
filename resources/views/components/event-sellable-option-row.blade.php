<div class="sellable-service-option-row mfw-line-separator pb-3" data-identifier="{{ $identifier }}">
    <div class="row nav nav-tabs">
        @foreach(config('mfw.translatable.locales') as $locale)
            @foreach($fillables as $key=>$value)
                <div class="col-sm-6">
                    <a class="nav-link active" href="#" style="cursor:default;">
                        <img src="{!! asset('vendor/flags/4x3/'.$locale.'.svg') !!}" alt="{{ trans('lang.'.$locale.'.label') }}" class="d-inline-block me-1 ше" style="height: 16px"/>
                        <span class="text-dark">{!! trans('lang.'.$locale.'.label') !!}</span>
                    </a>
                    <x-mfw::textarea height="100"
                                     name="service_option[{{ $random }}][{{ $key }}][{{ $locale }}]"
                                     :value="$option->translation($key, $locale)"
                                     label=''/>
                </div>
            @endforeach
        @endforeach
    </div>
    <div class="d-flex">
        <x-mfw::simple-modal id="delete_sellable_service_option"
                             class="btn btn-danger btn-sm mt-2"
                             title="Suppression d'une option"
                             confirmclass="btn-danger"
                             confirm="Supprimer"
                             callback="deleteSellableOption"
                             :identifier="$identifier"
                             :modelid="$option->id"
                             text="Supprimer"/>
    </div>
</div>

@pushonce('js')
    <script>
        const sellable_service_options = {
            container: function() {
                return $('#sellable-service-options');
            },
            addBtn: function () {
                return $('#add-sellable-service-option');
            },
            guid: function() {
                return guid();
            },
            add: function () {
                this.addBtn().off().on('click', function () {
                    sellable_service_options.container().append($('template#sellable-service-option-row').html());
                    let last_row = sellable_service_options.container().find('.sellable-service-option-row').last(),
                        guid = sellable_service_options.guid();
                    last_row.attr('data-identifier', guid);
                    last_row.find('a[data-modal-id=delete_sellable_service_option]').attr('data-identifier', guid);
                    last_row.find('textarea').each(function() {
                       $(this).attr('name', $(this).attr('name').replace('random', guid))
                    });
                    setDatepicker();
                });
            },
            init: function () {
                this.add();
            },
        };
        sellable_service_options.init();
    </script>
@endpushonce
