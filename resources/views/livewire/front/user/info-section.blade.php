@php
    use App\Accessors\Accounts;use App\Accessors\Dates;use App\Accessors\Front\FrontCache;use App\Enum\ParticipantType;use Illuminate\Support\Js;

    $profile = $account?->profile;
    $accountPhoto = $account ? Accounts::getPhotoByAccount($account) : null;

    $accountFirstName = ($account?->first_name === 'Votre prénom') ? '' : $account?->first_name;
    $accountLastName = ($account?->last_name === 'Votre nom') ? '' : $account?->last_name;

    $passportFirstName = old('passport_first_name', $account?->passport_first_name) ?: $accountFirstName;
    $passportLastName = old('passport_last_name', $account?->passport_last_name) ?: $accountLastName;


    if($registrationType){
        $participationGroup = match ($registrationType) {
            'participant','group','congress' => ParticipantType::CONGRESS->value,
            'industry' => ParticipantType::INDUSTRY->value,
            'speaker','orator' => ParticipantType::ORATOR->value,
            default => null,
        };
    }
    else{
        $participationGroup = null;
        if($eventContact) {
            $participationGroup = $eventContact->participationType->group;
        }
    }


@endphp

<div x-data="{
        first_name: {{ Js::from(old('first_name', $accountFirstName)) }},
        last_name: {{ Js::from(old('last_name', $accountLastName)) }},
        passport_first_name: {{ Js::from($passportFirstName) }},
        passport_last_name: {{ Js::from($passportLastName) }}
    }">


    <div class="card border">
        <div class="card-header border-bottom text-uppercase">
            <h5 class="mb-0 text-light-emphasis">{!! __('front/account.general_info') !!}</h5>
        </div>
        <div class="card-body">

            <div class="row mb-3">

                <label for="select_domain"
                       class="col-md-2 mt-3 mt-md-0 col-form-label text-start text-nowrap">{!! __('front/account.labels.domain')  !!}</label>
                <div class="col-md-4">
                    <x-mfw::select name="domain_id"
                                   :values="$event ? $event->domains->pluck('name','id')->sort()->toArray() : Dictionnaries::selectValues('domain')"
                                   :affected="old('domain_id', $profile?->domain_id)"/>

                    <small class="text-danger">{{ __('front/account.domain_warning') }}</small>
                </div>
            </div>

            <div class="row mb-3">
                <label for="select_title"
                       class="col-md-2 col-form-label text-start">{{ __("front/account.labels.title") }}</label>
                <div class="col-md-4">
                    <x-select-dictionary
                        key="titles"
                        name="title_id"
                        value="{{old('title_id', $profile?->title?->id)}}"
                        class="rounded-0 form-control"
                        id="select_title"/>
                </div>
                <label for="select_genre"
                       class="col-md-2 mt-3 mt-md-0 col-form-label text-start text-nowrap">{!! __('front/account.labels.genre') !!}</label>
                <div class="col-md-4">
                    <x-mfw::radio name="civ"
                                  :values="App\Enum\Civility::toArray()"
                                  default="M"
                                  :affected="old('civ', $profile?->civ)"/>
                </div>
            </div>

            <div class="row mb-3">
                <label for="input_first_name"
                       class="col-md-2 col-form-label text-start text-nowrap">{!! __('front/account.labels.first_name') !!}</label>
                <div class="col-md-4">
                    <input type="text"
                           name="first_name"
                           x-model="first_name"
                           @input="passport_first_name = first_name"
                           value="{{old('first_name', $accountFirstName)}}"
                           class="form-control rounded-0"
                           id="input_first_name">
                </div>
                <label for="input_last_name"
                       class="col-md-2 mt-3 mt-md-0 col-form-label text-start text-nowrap">{!! __('front/account.labels.last_name') !!}</label>
                <div class="col-md-4">
                    <input type="text"
                           name="last_name"
                           x-model="last_name"
                           @input="passport_last_name = last_name"
                           value="{{old('last_name', $accountLastName)}}"
                           class="form-control rounded-0"
                           id="input_last_name">
                </div>
            </div>

            <div class="row mb-3">
                <label for="input_birth"
                       class="col-md-2 mt-3 mt-md-0 col-form-label text-start text-nowrap">{!! __('front/account.labels.birth_date') !!}</label>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text"
                               name="birth"
                               value="{{old("birth", $profile?->birth?->format(Dates::getFrontDateFormat()))}}"
                               class="form-control rounded-0"
                               x-mask="{{Dates::getFrontDateFormat("x-mask")}}"
                               placeholder="{{Dates::getFrontDateFormat("placeholder")}}"
                               id="input_birth"
                        >
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="input_passport_first_name"
                       class="col-md-2 col-form-label text-start text-nowrap">{!! __('front/account.labels.passport_first_name') !!}</label>
                <div class="col-md-4">
                    <input type="text"
                           name="passport_first_name"
                           x-model="passport_first_name"
                           value="{{old('passport_first_name', $passportFirstName)}}"
                           class="form-control rounded-0"
                           id="input_passport_first_name">
                </div>
                <label for="input_passport_last_name"
                       class="col-md-2 mt-3 mt-md-0 col-form-label text-start text-nowrap">{!! __('front/account.labels.passport_last_name') !!}</label>
                <div class="col-md-4">
                    <input type="text"
                           name="passport_last_name"
                           x-model="passport_last_name"
                           value="{{old('passport_last_name', $passportLastName)}}"
                           class="form-control rounded-0"
                           id="input_passport_last_name">
                </div>
            </div>

            <div class="row mb-3">
                <label for="select_savant_society"
                       class="col-md-2 col-form-label text-start">{!! __('front/account.labels.savant_society') !!}</label>
                <div class="col-md-4">
                    <x-select-dictionary key="savant_societies"
                                         class="rounded-0 form-control"
                                         name="savant_society_id"
                                         id="select_savant_society"
                                         :value="old('savant_society_id', $profile?->savant_society_id)"/>
                </div>
                <label for="select_establishment"
                       class="col-md-2 mt-3 mt-md-0 col-form-label text-start text-nowrap">{!! __('front/account.labels.establishment') !!}</label>
                <div class="col-md-4">

                    <livewire:common.autocomplete

                        class="rounded-0 form-control"
                        name="establishment_id"
                        model-class="App\Accessors\Establishments"
                        get-items-method="orderedIdNameArray"
                        id="select_establishment"
                        :initial-value="(int)old('establishment_id', $profile?->establishment_id)"/>
                </div>
            </div>


            <div class="row mb-3">
                <label for="select_language_id"
                       class="col-md-2 col-form-label text-start text-nowrap">{!! __('front/account.labels.language') !!}</label>
                <div class="col-md-4">
                    <x-select-dictionary
                        key="language"
                        name="language_id"
                        value="{{old('language_id', $profile?->language_id)}}"
                        class="rounded-0 form-control"
                        id="select_language_id"/>
                </div>


                <label for="input_rpps"
                       class="col-md-2 col-form-label text-start text-nowrap">
                    {!! __('front/account.labels.rpps') !!}
                </label>
                <div class="col-md-4">
                    <input type="text"
                           name="rpps"
                           value="{{old('rpps', $profile?->rpps)}}"
                           class="form-control rounded-0"
                           id="input_rpps">

                    <small class="text-danger d-block pt-1">{!! __('front/account.labels.rpps_notice') !!}</small>
                </div>
            </div>

            @if(false)
                <div class="row mb-3">
                    <div class="col">

                        <div class="alert alert-warning">
                            {{__('front/account.labels.rpps_notice')}}
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mb-3">
                <label for="select_profession"
                       class="col-md-2 col-form-label text-start text-nowrap">{!! __('front/account.labels.profession') !!}</label>
                <div class="col-md-4">
                    <x-select-professions
                        :event="$event"
                        name="profession_id"
                        value="{{old('profession_id', $profile?->profession_id)}}"
                        class="rounded-0 form-control"
                        id="select_profession"/>
                </div>
                <label for="input_function"
                       class="col-md-2 mt-3 mt-md-0 col-form-label text-start text-nowrap">{!! __('front/account.labels.fonction') !!}</label>
                <div class="col-md-4">
                    <input type="text"
                           name="function"
                           value="{{old('function', $profile?->function)}}"
                           class="form-control rounded-0"
                           id="input_function">
                </div>
            </div>

        </div>
    </div>


</div>


