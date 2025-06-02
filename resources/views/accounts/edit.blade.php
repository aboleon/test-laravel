<x-backend-layout>
    @php
        $error = $errors->any();
    @endphp
    <x-slot name="header">
        <h2>
            {{ $account->id ? 'Édition' : 'Création' }} d'un compte
        </h2>


        <div class="d-flex align-items-center gap-1" id="topbar-actions" x-data>


            @if ($account->id)
                <a class="btn btn-sm btn-secondary mx-2"
                   href="{{ route('panel.accounts.index', ['role' => $account->profile->account_type] ) }}">
                    <i class="fa-solid fa-bars"></i>
                    Contacts {{ \App\Enum\ClientType::translated($account->profile->account_type) }}
                </a>

                <div class="dropdown ms-2">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-toggle="modal"
                            data-bs-target="#modal_actions_panel">
                        <i class="fa-solid fa-cog"></i>
                        Actions
                    </button>
                </div>
            @endif

            <div class="separator"></div>
            <a class="btn btn-sm btn-secondary" href="{{ route('panel.accounts.index', ['role' => $role]) }}">
                <i class="fa-solid fa-bars"></i>
                Index
            </a>
            @if ($account->id)
                <a class="btn btn-sm btn-success" href="{{ route('panel.accounts.create', ['role' => $role]) }}">
                    <i class="fa-solid fa-circle-plus"></i>
                    Créer
                </a>
            @endif
            <x-save-btns/>
            <div class="separator"></div>

        </div>
    </x-slot>

    @include('accounts.modal.action_panel')
    <div class="shadow p-3 mb-5 bg-body-tertiary rounded">
        <div class="row m-3">
            <div class="col">

                <x-mfw::validation-banner/>
                <x-mfw::response-messages/>
                @include('accounts.partials.edit_body')

            </div>
        </div>
    </div>
    @push('js')
        <script>
            activateEventManagerLeftMenuItem('accounts-{{ $role }}');
        </script>
    @endpush

</x-backend-layout>
