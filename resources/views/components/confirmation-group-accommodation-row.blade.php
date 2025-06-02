<tr>
    <td>{{ $eventContact->account->fullName() }}</td>
    <td style="text-align: left;padding-left: 20px;">
        <b>{{ __('front/cart.accommodation') }}</b><br>
        {{ $printableDate }} -
        {{ ($hotels[$attribution->room->group->accommodation->id] ?? '') }}<br>
        {{\App\Accessors\Dictionnaries::entry('type_chambres',  $attribution->room->room_id)->name . ' x ' .$attribution->room->capacity }}
        / {{ ($attribution->room->group->name ?? 'Inconnue') }}
    </td>
</tr>
