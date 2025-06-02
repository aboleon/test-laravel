<form method="post"
      action="{{ $data->id ? route('panel.groups.update', $data->id) : route('panel.groups.store') }}"
      id="wagaia-form" novalidate>
    @if($data->id)
        @method('put')
    @else
        <input type="hidden" name="created_by" value="{{auth()->user()->id}}"/>
        <input type="hidden" name="event_id" value="{{$eventId}}"/>
    @endif
    @csrf

    @if(isset($redirect_to))
        <input type="hidden" name="custom_redirect" value="{{ $redirect_to }}">
    @endif
    <fieldset>
        <legend>
            Groupe
            <x-front.debugmark :title="$data->id"/>
        </legend>

        <x-tab-cookie-redirect id="group" selector="#group-nav-tab-container .mfw-tab"/>

        <nav class="d-flex justify-content-between" id="group-nav-tab-container">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <x-mfw::tab tag="group-tabpane" label="Fiche" :active="true"/>
                <x-mfw::tab tag="adresses-tabpane" label="Adresses"/>
                <x-mfw::tab tag="contacts-tabpane" label="Contacts"/>
            </div>
            <div>
                @if($data->id)
                    <x-mfw::notice message="Fiche crée {{ App\Printers\UserRelated::creator($data) }}"/>
                @endif
            </div>
        </nav>
        <div class="tab-content mt-3" id="nav-tabContent">
            @include('groups.tabs.identity')
            @include('groups.tabs.address')
            @include('groups.tabs.contacts')
        </div>
    </fieldset>
</form>
