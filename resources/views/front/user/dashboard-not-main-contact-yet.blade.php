@php use App\Enum\RegistrationType;@endphp
<x-front-logged-in-layout
        :enable-order-btn="false"
        :event="$event"
        :group-view="true"
>
    <div class="row">

        @if(session("success"))
            <div class="col-12">
                <div class="alert alert-success">
                    {{session("success")}}
                </div>
            </div>
        @else
            <div class="col-12">
                @if($eventContact->fo_group_manager_request_sent)
                    <div class="alert alert-info">
                        Votre demande de management de groupe a bien été envoyée. Nous vous
                        contacterons prochainement.
                    </div>
                @else

                    <div class="alert alert-info">
                        Votre profil n'est pas encore rattaché à un groupe dans notre base de
                        données.
                        <br>
                        Vous ne pouvez donc pas gérer votre groupe ni faire de commande en ligne.
                        <br>
                        <br>
                        Merci de compléter le formulaire suivant et l'un des membres de notre équipe
                        reviendra vers vous rapidement.
                    </div>

                    <form action="{{route('front.event.sendMainContactMail', $event)}}"
                          method="POST"
                          class="m-auto">
                        @csrf

                        <x-front.form-errors />


                        <div class="mb-3">
                            <label for="input-name" class="form-label">Nom de l'entité qui va
                                inscrire
                                et
                                payer les inscriptions et/ou hébergements
                                <span class="text-danger">*</span></label>
                            <input value="{{old('name')}}"
                                   name="name"
                                   type="text"
                                   class="form-control"
                                   id="input-name">
                        </div>
                        <div class="mb-3">
                            <label for="area-address" class="form-label">Adresse postale
                                <span class="text-danger">*</span></label>
                            <textarea name="address"
                                      class="form-control"
                                      id="area-address"
                                      rows="3">{{old('address')}}</textarea>
                        </div>

                        <div class="row mb-3">

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="input-cp" class="form-label">CP
                                        <span class="text-danger">*</span></label>
                                    <input value="{{old('zip')}}"
                                           name="zip"
                                           type="text"
                                           class="form-control"
                                           id="input-cp">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="input-city" class="form-label">Ville
                                        <span class="text-danger">*</span></label>
                                    <input value="{{old('city')}}"
                                           name="city"
                                           type="text"
                                           class="form-control"
                                           id="input-city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="input-country" class="form-label">Pays
                                        <span class="text-danger">*</span></label>
                                    <input name="country"
                                           value="{{old('country')}}"
                                           type="text"
                                           class="form-control"
                                           id="input-country">
                                </div>
                            </div>
                        </div>


                        <div class="input-group mb-3">
                            @php
                                $cc2ccc = \App\Helpers\PhoneCountryHelper::selectable();
                            @endphp
                            <select class="input-group-text w-150px" name="country_code">
                                <option value="">Indicatif</option>
                                @foreach($cc2ccc as $cc => $ccc)
                                    @php
                                        $old = old('country_code');
                                        $sSelected = $old === $cc ? 'selected' : '';
                                    @endphp
                                    <option value="{{$cc}}" {{$sSelected}}>{{$ccc}}</option>
                                @endforeach
                            </select>
                            <input type="text"
                                   name="phone"
                                   value="{{old('phone')}}"
                                   class="form-control"
                                   placeholder="Téléphone du siège">
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </div>
                    </form>
                @endif

            </div>
        @endif
    </div>


</x-front-logged-in-layout>
