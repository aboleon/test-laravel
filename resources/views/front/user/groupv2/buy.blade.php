@php
    use App\Accessors\Front\FrontCartAccessor;
    use App\Helpers\Front\Cart\FrontGroupCart;
    $groupCart = FrontGroupCart::getInstance($eventContact);
@endphp
<x-front-logged-in-group-manager-v2-layout :event="$event">
    <x-front.session-notifs/>
    <h3 class="main-title ms-3">Mes achats de groupe</h3>

    <div class="card border container-members my-3 mx-3">
        <div class="card-body">
            <div class="row">

                @if($groupMembers->isNotEmpty())
                    <div class="col-12 text-dark fw-bold">Mettre des produits au panier pour:</div>
                    <div class="col-12 mt-2 d-flex gap-2 flex-wrap">
                        <ul class="m-0">
                            @foreach($groupMembers as $groupMember)
                                <li>
                                    <a
                                            class="action-switch-to-user"
                                            href="{{route('front.event.switch-to-group-member', [
                'event' => $event->id,
                'group' => $groupMember->group_id,
                'user' => $groupMember->user_id,
                'routeType' => 'services',
            ])}}">{{ $groupMember->last_name . " " . $groupMember->first_name }}</a>
                                    <div class="spinner-border spinner-border-sm"
                                         role="status"
                                         style="display:none;">
                                        <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                                    </div>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="alert alert-danger">
                        Veuillez ajouter des membres à votre groupe pour pouvoir acheter des
                        produits.
                    </div>
                @endif
            </div>


        </div>
    </div>

    <livewire:front.cart.inline-group-cart :eventGroup="$eventGroup"
                                           :eventContact="$eventContact"/>


    @push("js")
        <script>
            $(document).ready(function () {
                $('.container-members').on('click', function (e) {
                    let jTarget = $(e.target);
                    if (jTarget.hasClass('action-switch-to-user')) {
                        let jSpinner = jTarget.next('.spinner-border');
                        jSpinner.show();
                    }
                });
            });
        </script>
    @endpush


</x-front-logged-in-group-manager-v2-layout>
