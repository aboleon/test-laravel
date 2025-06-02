<div class="row">
    <div class="col-12">
        @forelse($eventGroupContacts as $contact)
            <x-mfw::checkbox :value="$contact->id" name="contacts." :label="$contact->account->names()" />
        @empty
            <p>Aucun participants dans ce groupe.</p>
        @endforelse
    </div>
</div>
