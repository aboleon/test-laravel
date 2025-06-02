@php use App\Accessors\Dates; @endphp
<h5 class="mt-3">{{__('front/transport.step_1_info')}}</h5>

<form class="mt-3"
      method="POST"
      action="{{route('front.event.transport.update.divine.step.info', $event) }}"
>
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{__('front/transport.labels.passport_last_name')}}</label>
            <input type="text"
                   class="form-control"
                   name="passport_last_name"
                   value="{{ old('passport_last_name', $profile?->passport_last_name) }}"
            >
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">{{__('front/transport.labels.passport_first_name')}}</label>
            <input type="text"
                   class="form-control"
                   name="passport_first_name"
                   value="{{ old('passport_first_name', $profile?->passport_first_name) }}"
            >
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{__('account.birth')}}</label>
            <div class="input-group">
                <input type="text" class="form-control"
                       x-mask="{{ Dates::getFrontDateFormat("x-mask") }}"
                       placeholder="{{ Dates::getFrontDateFormat("placeholder") }}"
                       wire:model.debounce.1ms="emitted_at"
                       name="birth"
                       value="{{ old('birth', $profile?->birth?->format(config('app.date_display_format'))) }}"
                >
                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
            </div>
        </div>


        <div class="col-md-6 mb-3">
            <label class="form-label">{{__('front/transport.labels.travel_preferences')}}</label>
            <textarea
                name="travel_preferences"
                class="form-control"
                rows="3">{{ old('travel_preferences', $transport?->travel_preferences)}}</textarea>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <livewire:front.user.loyalty-card-section :account="$account"/>
            <livewire:front.user.identity-card-section :account="$account"/>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        <button
            type="submit"
            class="btn btn-primary next-btn mb-0">
            {{__('front/transport.next')}}
        </button>
    </div>

</form>

