@php use App\Enum\RegistrationType; @endphp
<x-front-logged-in-layout :event="$event">
    <div class="row">

        <div class="col-12">
            <div class="alert alert-info">
                Veuillez choisir votre type de participation <b>pour cet événement</b> pour
                continuer.
            </div>

            <form action="{{route('front.event.registerParticipationType', $event)}}"
                  method="POST"
                  class="m-auto">
                @csrf
                @method('PUT')

                <x-front.form-errors/>
                <div class="row">

                    <label for="select_participation_type"
                           class="col-md-2 col-form-label text-start ">Type de participation</label>
                    <div class="col-md-4">
                        <x-select-participation-type name="participation_type"
                                                     value="{{  old('participation_type') }}"
                                                     :event="$event"
                                                     :group="$registrationType"
                                                     :show-groups="is_null($registrationType)"
                                                     :exclude-groups="['orator']"
                                                     class="rounded-0 form-control"
                                                     id="select_participation_type"/>


                    </div>
                    <div class="col-md-6 text-start mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>

            </form>

        </div>

    </div>


</x-front-logged-in-layout>
