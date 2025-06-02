<style>
    .bs-stepper .step-trigger:not(:disabled):not(.disabled) {
        cursor: default;
    }
</style>
<div class="bs-stepper-header" role="tablist">
    @for($i = 1; $i <= $nbSteps; $i++)
        @if(1 !== $i)
            <div class="line"></div>
        @endif
        <div class="step"
             :class="{ 'active': step === {{$i}} }">
            <div class="d-grid text-center align-items-center">
                <span
                   data-url="{{request()->fullUrlWithQuery(["step" => $i])}}"
                   class="btn btn-link step-trigger mb-0"
                   role="tab"
                   id="steppertrigger{{$i}}"
                   aria-controls="step-{{$i}}"
                   aria-selected="true">
                    <span class="bs-stepper-circle">{{$i}}</span>
                </span>
            </div>
        </div>
    @endfor
</div>
