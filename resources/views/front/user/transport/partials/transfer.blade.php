@php
    $transferRequested = old('transfer_requested', $transport?->transfer_requested);
    if(!$transferRequested){
        $transferRequested = 0;
    }
    $showReimbursementInfo = $showReimbursementInfo??  true;
@endphp
<h5 class="mt-3">{{__('front/transport.step_4_transfer')}}</h5>

<form class="mt-3"
      method="POST"
      action="{{$actionUrl}}">
    @csrf
    @method('PUT')

    <p class="fw-bold text-black">
        {{__('front/transport.do_you_need_transfer_to_the_congress_location')}}
    </p>
    <div>
        <div class="form-check">
            <input class="form-check-input"
                   type="radio"
                   name="transfer_requested"
                   value="1"
                   id="flexRadioDefault1"
                    {{ old('transfer_requested', $transferRequested) == '1' ? 'checked' : '' }}
            >
            <label class="form-check-label" for="flexRadioDefault1">
                Oui
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input"
                   type="radio"
                   name="transfer_requested"
                   value="0"
                   id="flexRadioDefault2"
                    {{ old('transfer_requested', $transferRequested) == '0' ? 'checked' : '' }}
            >
            <label class="form-check-label" for="flexRadioDefault2">
                Non
            </label>
        </div>
    </div>
    <p class="fw-bold text-danger mt-3">
        <i class="bi bi-exclamation-circle"></i>
        {{__('front/transport.valid_for_go_and_return_for_1_person')}}
    </p>
    <div class="d-flex justify-content-center mt-3 gap-2">
        <button @click="step=3"
                type="button"
                class="btn btn-primary next-btn mb-0">
            {{__('front/transport.previous')}}
        </button>
        <button
                type="submit"
                class="btn btn-primary next-btn mb-0">
            {{__('front/transport.next')}}
        </button>
    </div>

</form>

@if($showReimbursementInfo)
    <p class="mt-4 fw-bold text-danger">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <i>{{__('front/transport.please_complete_your_request_to_get_reimbursement')}}</i>
    </p>
@endif
