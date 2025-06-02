@if($room_notes->isNotEmpty())
    <br />
    @foreach($room_notes as $room_note)
            Commentaire : {{$room_note->note}}@if(!$loop->last)<br /> @endif
    @endforeach
@endif
