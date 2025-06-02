<div class="row" id="order-subjects">
    <div class="col-lg-4" id="client-type-selector">
        <x-mfw::radio name="order.client_type"
                      :values="['contact' => 'Participant', 'group' => 'Groupe']"
                      :affected="$error ? old('order.client_type') :'contact'"/>
    </div>
    <div class="col-lg-8" id="client-type-subselector">
        <div id="order_affectable_contact">
            <x-mfw::select name="order.contact_id" :values="[]"/>
        </div>
        <div id="order_affectable_group"
             class="d-none">
            <x-mfw::select name="order.group_id" :values="[]"/>
        </div>
    </div>
</div>

<x-mfw::input name="participation_type" label="Type de participation" :readonly="true"/>

<div id="account_info" class="mt-3 g-0 row align-items-center"></div>
