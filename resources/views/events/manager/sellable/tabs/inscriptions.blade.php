@php
    use App\Accessors\EventManager\Sellable\EventContactSellableServiceChoosables;
    use App\Enum\ApprovalResponseStatus;use App\Models\EventManager\Sellable\EventContactSellableServiceChoosable;
    $error = $errors->any();
@endphp
<div class="tab-pane fade"
     id="inscriptions-tabpane"
     role="tabpanel"
     aria-labelledby="inscriptions-tabpane-tab">

    @if($data->id)

        @include('events.manager.sellable.tabs.inscriptions.'.($data->is_invitation ? 'invitation' : 'sellable').'-inscriptions')
    @else
        <x-mfw::notice message="Veuillez d'abord enregistrer la prestation."/>
    @endif


</div>
