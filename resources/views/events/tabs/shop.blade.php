<div class="tab-pane fade" id="shop-tabpane" role="tabpanel" aria-labelledby="shop-tabpane-tab">
    <div class="row pt-4">
        <div class="col-xl-6">
            <div class="row">
                <div class="col-12 mb-3">
                    <x-mfw::checkbox :switch="true" name="event[shop][is_active]" value="1" label="Activer boutique Exposants" :affected="collect($error ? old('event.shop.is_active') : ($data->id ? $data->shop?->is_active : 1))"/>
                </div>
                <div class="col-xl-6 mb-3">
                    <x-mfw::select name="event[shop][admin_id]" label="Admin exposant" :values="$admin_users" :affected="$error ? old('event.shop.admin_id') : $data->shop?->admin_id"/>
                </div>

                <div class="col-xxl-6 mb-3">
                    <x-mfw::datepicker label="Date limite d'achat en ligne" name="event[shop][shopping_limit_date]" :value="$error ? old('event.shop.shopping_limit_date') : $data->shop?->shopping_limit_date"/>
                </div>

                <div class="col-12 mb-2 mt-3 mfw-holder position-relative" data-callback="append_shop_doc">
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="w-100 me-3">
                            <x-selectable-dictionnary key="documents_for_exhibitors" name="event[select][documents_for_exhibitors]" :affected="$error ? old('event.shop.documents_for_exhibitors') : $data?->shop?->documents_for_exhibitors"/>
                        </div>
                        <span class="fs-4 add-dynamic dict-dynamic" data-dict="documents_for_exhibitors"><i class="fa-solid fa-circle-plus"></i></span>
                    </div>
                </div>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-success btn-sm" id="add_shop_doc_from_select">Ajouter depuis la liste existante</button>
                </div>
                <div id="affected_shop_docs">
                    @forelse($data->shopDocs as $doc)
                        <div class="row align-items-center text-black fw-bold shop-doc-{{ $doc->id }}">
                            <div class="col-10">{{ $doc->name }}</div>
                            <div class="col-2">
                                <input type="hidden" name="shop_docs[]" value="{{ $doc->id }}"/>
                                <ul class="mfw-actions mb-2">
                                    <li data-bs-toggle="modal" data-bs-target="#destroy_shop_range_modal" data-target="shop-doc-{{ $doc->id }}">
                                        <a href="#" class="btn btn-sm btn-danger" data-bs-placement="top" data-bs-title="Supprimer le document ?" data-bs-toggle="tooltip"><i class="fas fa-trash"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @empty
                        <x-mfw::alert message="Vous n'avez affecté aucun document"/>
                    @endforelse
                </div>
                <div class="mfw-line-separator mt-4 mb-4"></div>
                <div class="col-12 mb-3 mt-3">
                    <x-mediaclass::uploadable :model="$data" group="for_exhibitors" label="Document à déposer en front pour tous les exposants"/>
                </div>
                <div class="mfw-line-separator mt-2 mb-5"></div>
                <div class="col-12 mb-3">
                    <x-mfw::translatable-tabs :fillables="$data->fillables['shop']" id="shop" datakey="event[texts]" :model="$texts"/>
                </div>
            </div>
        </div>
        <div class="col-xl-6 ps-xl-5">
            <h4>Frais de port, par commande</h4>
            <div class="row">

                <div class="col-xl-6 mb-3">
                    <x-mfw::select name="event[shop][vat_id]" :values="\MetaFramework\Accessors\VatAccessor::selectables()" :affected="$error ? old('event.shop.vat_id') : $data->shop?->vat_id" label="TVA appliquée"/>
                </div>
                <div class="col-12" id="shopping_modes">
                    <x-mfw::radio name="event[shop][shopping_mode]" :values="\App\Enum\EventShoppingMode::translations()" :affected="$error ? old('event.shop.shopping_mode') : $data->shop?->shopping_mode" :default="\App\Enum\EventShoppingMode::default()"/>
                    <div id="shopping_mode_fixed" class="submodes{{ $data->shop?->shopping_mode =='custom' ? 'd-none ' : '' }}">
                        <x-mfw::input type="number" :value="$error ? old('event.shop.fixed_fee') : ($data->shop?->fixed_fee ?: 0)" :param="['min'=>1]" name="event[shop][fixed_fee]" label="Montant € TTC"/>
                    </div>
                    <div id="shopping_mode_custom" class="submodes {{ $data->shop?->shopping_mode !='custom' ? 'd-none ' : '' }}my-3">
                        @if ($data->shopRanges->isEmpty())
                            <x-mfw::notice message="Aucune tranche n'est saisie"/>
                        @else
                            <div class="rows">
                                @foreach($data->shopRanges as $range)
                                    <x-shop-range-fee :range="$range"/>
                                @endforeach
                            </div>
                        @endif
                        <button class="btn btn-sm btn-success mt-3" id="add_custom_shopping_fee" type="button">Ajouter</button>
                        <template id="shopping_mode_custom_template">
                            <x-shop-range-fee :range="new \App\Models\EventShoppingRanges()"/>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mfw-line-separator my-4"></div>
            <div class="d-flex justify-content-between">
                <h4>Configuration Catalogue</h4>
                <div>
                    <a class="btn btn-danger btn-sm" href="{{ route('panel.sellables.index') }}" target="_blank">Gestion catalogue</a>
                </div>
            </div>
            @include('events.inc.catalog')

        </div>

    </div>
</div>

<div class="modal fade" id="destroy_shop_range_modal" tabindex="-1" aria-labelledby="shop_range_modalLabel" aria-hidden="true">
    <form>
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shop_range_modalLabel">Supprimer cette ligne ?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.close') }}"></button>
                </div>
                <div class="modal-body" id="shop_range_modalBody"></div>
                <div class="modal-footer d-flex justify-content-between" id="shop_range_modalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="shop_range_modalSave">
                        <i class="fa-solid fa-check"></i> {{ __('ui.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
