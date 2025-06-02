@if($accompanying_notes->isNotEmpty())
    <br />
    @foreach($accompanying_notes as $accompanyingNote)
        Accompagnant : {{$accompanyingNote->total}} {{$accompanyingNote->names}}@if(!$loop->last)<br /> @endif
    @endforeach
@endif
