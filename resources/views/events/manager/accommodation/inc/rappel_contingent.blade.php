<div class="wg-card my-4 accordion">
    <div class="accordion-item">
        <h3 class="accordion-header m-0">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                Rappel des contingents
            </button>
        </h3>
        <div id="collapseOne" class="accordion-collapse" data-bs-parent="#accordionExample">
            <div class="accordion-body">
                @if ($room_groups)

                    @include('events.manager.accommodation.inc.general_recap')

                @else
                    <x-mfw::notice message="Aucune configuration de chambres n'est enregistrée"/>
                @endif
            </div>
        </div>
    </div>
</div>
