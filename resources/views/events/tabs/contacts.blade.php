<div class="tab-pane fade"
     id="contacts-tabpane"
     role="tabpanel"
     aria-labelledby="contacts-tab">

    @if ($data->id)
        <x-ajaxable-contacts :contacts="$contacts" :useIsMainContact="false" :model="$data" query-tag="event"/>
    @else
        <x-mfw::notice message="Vous pourrez associer des clients après que l'évènement ait été créé."/>
    @endif
</div>


