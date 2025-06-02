<h4>Informations de l'hébergement</h4>
<div class="row my-4">
    <div class="col-lg-6 mb-3">
        <x-mfw::input name="name" :label="__('account.last_name')" :value="$error ? old('name') : $accommodation->hotel->name" :params="['disabled'=>true]"/>
    </div>

    <div class="col-lg-6 mb-3">
        <x-mfw::select :values="\App\Enum\Stars::toArray()" name="stars" label="{{__('forms.fields.stars')}}" :affected="$accommodation->hotel->stars" :nullable="true" defaultselecttext="{{__('ui.hotels.no_ranking')}}" :params="['disabled'=>true]"/>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 mb-3">
        <h4>{{__('ui.hotels.address')}}</h4>

        <p class="fw-bold text-dark">{{ $accommodation->hotel->address->text_address }}</p>
    </div>
</div>

<x-mfw::translatable-tabs id="hotel_presentation" :fillables="$accommodation->hotel->fillables" :model="$accommodation->hotel" :disabled="true"/>


<div class="row mb-4">
    <div class="col-lg-12 mb-3">
        <h4>{{__('ui.hotels.services')}}</h4>
        <div class="col-lg-12 mb-3 d-flex align-align-items-center gap-3">
            @forelse(\App\Accessors\Dictionnaries::dictionnary('hotel_service')->entries as $values)
                <x-mfw::checkbox name="services[]" :value="$values->id" :label="$values->name" :affected="collect($accommodation->hotel->services)" :params="['disabled'=>true]"/>
            @empty
                {{__('ui.hotels.no_services')}}
            @endforelse
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-lg-12 mb-3">
        <h4>{{__('ui.hotels.contacts')}}</h4>
    </div>
    <div class="col-lg-6 mb-3">
        <x-mfw::input name="first_name" :params="['disabled'=>true]" :label="__('account.first_name')" :value="$accommodation->hotel->first_name"/>
    </div>
    <div class="col-lg-6 mb-3">
        <x-mfw::input name="last_name" :params="['disabled'=>true]" :label="__('account.last_name')" :value="$accommodation->hotel->last_name"/>
    </div>
    <div class="col-lg-6 mb-3">
        <x-mfw::input name="phone" :params="['disabled'=>true]" :label="__('account.phone')" :value="$accommodation->hotel->phone"/>
    </div>
    <div class="col-lg-6 mb-3">
        <x-mfw::input name="email" :params="['disabled'=>true]" type="email" :label="__('ui.email_address')" :value="$accommodation->hotel->email"/>
    </div>
</div>

<div class="text-center">
    <a class="btn btn-secondary" href="{{ route('panel.hotels.edit', [$accommodation->hotel, 'save_and_redirect' => route('panel.manager.event.accommodation.edit', [$event, $accommodation])]) }}">Modifier les informations de base</a><br>
    <p class="d-block mt-2 text-secondary">* pensez à sauvegarder les informations déjà saisies</p>
</div>
