@php
    use App\Accessors\Dates;
@endphp
<form class="mt-3"
      method="POST"
      action="{{$actionUrl}}">
    @csrf
    @method('PUT')
    <p class="fw-bold text-black">{{$title}}</p>
    <div class="row">
        <div class="col-md-4 mb-3">
            <x-mfw::datepicker name="return_start_date"
                               label="{{  __('front/transport.labels.start_date') }}"
                               value="{{ old('return_start_date', $transport?->return_start_date?->format(config('app.date_display_format'))) }}"
                               config="dateFormat={{config('app.date_display_format')}},defaultDate={{ old('return_start_date', $transport?->return_start_date?->format(config('app.date_display_format'))) }}"/>
            @if(($transport?->departure_start_date && $transport?->return_start_date) && $transport->return_start_date->isBefore($transport->departure_start_date))
                    <div class="invalid-feedback d-block">{!! __('transport.errors.departure_date_over_start_date') !!}</div>
                @endif
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">{{__('front/transport.labels.start_hour')}}</label>
            <div class="input-group">
                <input type="text" class="form-control"
                       name="return_start_time"
                       value="{{old('return_start_time', $transport?->return_start_time?->format('H:i'))}}"
                       x-mask="{{Dates::getFrontHourMinuteFormat("x-mask")}}"
                       placeholder="{{Dates::getFrontHourMinuteFormat("placeholder")}}">
                <span class="input-group-text"><i class="bi bi-alarm"></i></span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">{{__('front/transport.labels.end_hour')}}</label>
            <div class="input-group">
                <input type="text" class="form-control"
                       name="return_end_time"
                       value="{{old('return_end_time', $transport?->return_end_time?->format('H:i'))}}"
                       x-mask="{{Dates::getFrontHourMinuteFormat("x-mask")}}"
                       placeholder="{{Dates::getFrontHourMinuteFormat("placeholder")}}">
                <span class="input-group-text"><i class="bi bi-alarm"></i></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <x-mfw::input :label="__('front/transport.labels.departure_start_location')"
                          name="return_start_location"
                          :value="old('return_start_location', $transport?->return_start_location)"/>

        </div>
        <div class="col-md-6 mb-3">
            <x-mfw::input :label="__('front/transport.labels.departure_end_location')"
                          name="return_end_location"
                          :value="old('return_end_location', $transport?->return_end_location)"/>

        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{__('front/transport.labels.departure_transport_type')}}</label>
            <x-select-dictionary :value="old('return_transport_type', $transport?->return_transport_type)"
                                 name="return_transport_type"
                                 key="transport"
                                 class="form-select"/>
        </div>

        <div class="col-12 mb-3">
            <x-mfw::textarea :label="__('front/transport.labels.departure_participant_comment')"
                             name="return_participant_comment"
                             height="100"
                             :value="old('return_participant_comment', $transport?->return_participant_comment)"/>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3 gap-2">
        @if(isset($btnPrevious))
            <button @click="step={{$btnPrevious}}"
                    type="button"
                    class="btn btn-primary next-btn mb-0">
                {{__('front/transport.previous')}}
            </button>
        @endif
        <button type="submit"
                class="btn btn-primary next-btn mb-0">
            {{__('front/transport.next')}}
        </button>
    </div>
</form>
