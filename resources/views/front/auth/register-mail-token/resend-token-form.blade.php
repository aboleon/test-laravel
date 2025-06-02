<form
        method="POST"
        action="{{ route('front.event.registerByEmail', $event) }}"
        novalidate
>
    @csrf

    <input type="hidden" name="registration_type" value="{{$registrationType}}">
    <input type="hidden" name="event_id" value="{{$event->id}}">
    <input type="hidden" name="email" value="{{$email}}">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
        <button type="submit" class="w-auto mt-3 mt-md-0 btn btn-primary rounded-0">
            {{__('front/register.send_again')}}
        </button>
    </div>
</form>