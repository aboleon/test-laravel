<div class="tab-pane fade"
     id="adresses-tabpane"
     role="tabpanel"
     aria-labelledby="adresses-tab">

    <fieldset class="my-5">
            <div class="row">
                    @forelse($data->address as $item)
                        <div class="address-block col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    {!! \App\Printers\Groups::address($item) !!}
                                    <div class="mt-3 d-flex justify-content-between">
                                        <a class="fw-bold link-dark" href="{{ route('panel.groups.addresses.edit', [$data, $item]) }}">Modifier</a>
                                        <a class="fw-bold link-danger" data-bs-toggle="modal" data-bs-target="#destroy_address_{{ $item->id }}" href="#">{{ __('ui.delete') }}</a>
                                    </div>
                                    @push('modals')
                                        <x-mfw::modal :route="route('panel.groups.addresses.destroy', [$data, $item])" reference="destroy_address_{{ $item->id }}"/>
                                    @endpush
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col">
                        {!! wg_warning_notice("Aucune adresse saisie") !!}
                        </div>
                    @endforelse
                </div>
                @if($data->id)
                    <a href="{{route('panel.groups.addresses.create', $data)}}" class="btn btn-sm rounded-1 btn-secondary d-inline-block">Ajouter une nouvelle adresse</a>
                @else
                    <p class="text-dark fw-bold">Vous pourrez ajouter des adresses une fois le groupe créé.</p>
                @endif
    </fieldset>
</div>
