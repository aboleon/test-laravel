@php
    use App\Accessors\GroupAccessor;
    use App\Accessors\EventContactAccessor;
    use App\Enum\DesiredTransportManagement;
    use MetaFramework\Accessors\Prices;
@endphp
<div class="row mb-3 tr-base">
    <div class="card">
        <div class="card-body">
            <h5 class="my-3">{{__('transport.visible_by_admin_only')}}</h5>
            <div class="mfw-line-separator mb-5"></div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <h4>{{__('transport.financing')}}</h4>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-mfw::number name="item.main.price_before_tax"
                                           class="modelUpdate"
                                           step="0.01"
                                           :value="old('item.main.price_before_tax',$transport?->price_before_tax)"
                                           :label="__('transport.price_before_tax')"
                                           :params="[
                                                                           'data-model' => \App\Models\EventManager\Transport\EventTransport::class,
                                                                           'data-id'=> $transport?->id,
                                                                           'data-column' => 'price_before_tax'
                                                                       ]"/>
                        </div>
                        <div class="col-md-6 mb-3">
                            <x-mfw::number name="item[main][price_after_tax]"
                                           class="modelUpdate"
                                           step="0.01"
                                           :value="old('item.main.price_after_tax', $transport?->price_after_tax)"
                                           :label="__('front/order.amount_total')"
                                           :params="[
                                                                           'data-model' => \App\Models\EventManager\Transport\EventTransport::class,
                                                                           'data-id'=> $transport?->id,
                                                                           'data-column' => 'price_after_tax'
                                                                       ]"/>
                        </div>
                        <div
                            class="col-12 mb-3 {{ $transportAccessor->isDivine() ? 'd-none' : '' }}">
                            <x-mfw::number name="item[main][max_reimbursement]"
                                           step="0.01"
                                           :value="old('item.main.max_reimbursement', $transport?->max_reimbursement)"
                                           label="Remboursement max si non éligible PEC"
                                           :params/>
                        </div>
                        <div class="col-12">
                            <x-mfw::textarea
                                label="{{__('transport.comment_not_visible_by_pax')}}"
                                height="100"
                                name="item[main][admin_comment]"
                                :value="old('item.main.admin_comment', $transport?->admin_comment)"/>
                        </div>
                    </div>

                </div>
                <div class="col-md-6 position-relative" id="grant-transport-container">
                    @include('events.manager.transport.include.grants')
                </div>
            </div>
        </div>
    </div>
</div>
