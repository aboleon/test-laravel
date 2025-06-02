@if($attributions->isNotEmpty())
    <br />
        Attributions :
    @foreach($attributions as $attribution)
            {{$attribution->eventContact->user->first_name}} {{$attribution->eventContact->user->last_name}}@if(!$loop->last), @endif
    @endforeach
@endif
