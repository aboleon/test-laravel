<div class="tab-pane fade"
     id="contacts-tabpane"
     role="tabpanel"
     aria-labelledby="contacts-tab">

    @php
        $eventGroup = isset($eventGroup) ? $eventGroup : null;
    @endphp


    @if ($data->id)
    <x-ajaxable-contacts :useIsMainContact="false" :eventGroup="$eventGroup" query-tag="group" :contacts="$contacts" :model="$data" />@else
        <x-mfw::notice message="Vous pourrez associer des contacts après que le groupe ait été créé."/>
    @endif
</div>
