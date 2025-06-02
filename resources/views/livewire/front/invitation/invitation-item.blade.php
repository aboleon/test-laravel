@php
    use App\Accessors\EventManager\Sellable\EventContactSellableServiceChoosables;
    use App\Enum\ApprovalResponseStatus;

    $info = EventContactSellableServiceChoosables::getInfoByEventContactAndChoosable($eventContact, $item);
    $status = $info['status']??null;
    $invitationQuantityAccepted = $info['invitation_quantity_accepted']??null;
    $bInvitationQuantityAccepted = (bool)$invitationQuantityAccepted;

@endphp
<div class="row mb-3">
    <div class="col-12">
        <div class="card border">

            <div class="card-body">
                <div class="card-title fs-4 divine-secondary-color-text border-bottom pb-2">
                    {{$item->title}}
                    <x-front.debugmark :title="$item->id" />
                </div>
                <div class="d-flex gap-3">
                    @if($item->service_date)
                        <div>
                            <i class="bi bi-calendar3-event me-1"></i> {{$item->service_date}}
                        </div>
                    @endif

                    @if($item->service_starts)
                        <div>
                            <i class="bi bi-alarm"></i> {{$item->service_starts->format('H\hi')}}
                            @if($item->service_ends)
                                - {{$item->service_ends->format('H\hi')}}
                            @endif
                        </div>
                    @endif

                    @if($item->place)
                        <div>
                            <i class="bi bi-geo-alt-fill"></i> {{$item->place->name}}
                            @if($item->room)
                                - {{$item->room->name}}
                            @endif
                        </div>
                    @endif
                </div>
                <p class="mt-4">
                    {{$item->description}}
                </p>

                <div class="mb-2 d-flex align-items-center gap-3">
                    <span class="d-inline">{{__('front/invitations.current_status')}}:</span>
                    <span class="fs-6">
                        @switch($status)
                            @case(ApprovalResponseStatus::VALIDATED->value)
                                <i class="bi bi-check-circle" style="color: green"></i>
                                <span class="text-dark fw-bold">{{__('front/invitations.accepted')}}</span>
                                @break
                            @case(ApprovalResponseStatus::DENIED->value)
                                <i class="bi bi-x-circle" style="color:red"></i>
                                <span class="text-dark fw-bold">{{__('front/invitations.denied')}}</span>
                                @break
                            @default
                                <i class="bi bi-question-circle"
                                   style="color: blue"></i>
                                <span class="text-dark fw-bold">{{__('front/invitations.pending')}}</span>
                                @break
                        @endswitch
                    </span>
                </div>
                <div class="mb-2 mt-4">
                    <div class="d-flex gap-1 align-items-center">
                        <a href="#"
                           wire:click.prevent="accept"
                           class="btn btn-success btn-sm smaller">{{__('front/invitations.accept')}}</a>
                        <a href="#"
                           wire:click.prevent="deny"
                           class="btn btn-danger btn-sm smaller">{{__('front/invitations.deny')}}</a>
                        <x-front.livewire-ajax-spinner target="deny,accept" />
                    </div>
                </div>
                @if($item->invitation_quantity_enabled && ApprovalResponseStatus::VALIDATED->value === $status)
                    <div class="form-check">
                        <input wire:change="updateQuantityAccepted({{(int)!$bInvitationQuantityAccepted}})"
                               :checked="{{$bInvitationQuantityAccepted}}"
                               class="form-check-input"
                               type="checkbox"
                               value="1"
                               id="invitation-{{$item->id}}-quantity_enabled">
                        <label class="form-check-label"
                               for="invitation-{{$item->id}}-quantity_enabled">
                            {{__('front/invitations.i_come_accompanied')}}
                        </label>
                        <x-front.livewire-ajax-spinner target="updateQuantityAccepted" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
