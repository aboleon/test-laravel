<div class="tab-pane fade" id="address-tabpane" role="tabpanel" aria-labelledby="address-tabpane-tab">
    <h4 class="mb-4">Adresse de facturation</h4>
    <div class="row">
        <div class="col-sm-6">

            <x-mfw::google-places :geo="$address" />

            <x-mfw::textarea name="wa_geo.complementary" label="Complément d'adresse" :value="$address?->complementary" />
        </div>
    </div>
</div>
