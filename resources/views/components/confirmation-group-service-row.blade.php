<tr>
    <td>{{ $eventContact->account->fullName() }}</td>
    <td style="text-align: left;padding-left: 20px">
        <b>{{ __('front/cart.service') }}</b><br>
        <span class="main">{{  $sellable?->title ?? 'NC' }} - {{ $group?->name }}</span>
    </td>
</tr>
