@php
    $account_roles = $account->id ? $account->roles : [['role_id' => $role['id']]];
    $error = $errors->any();
@endphp

@forelse($account_roles as $account_role)

    @php
        $filtered_role = array_filter($roles, fn($item) => $item['id'] == $account_role['role_id']);
        $subclass = $account->userSubData((string)(key($filtered_role)));
    @endphp

    @if($subclass)
        <fieldset>
            <input type="hidden" name="has_account_profile" value="1" />
            <legend>Informations complémentaires {{ $account->roles->count() > 0 ? current($filtered_role)['label'] : '' }}</legend>
            <div class="row gx-5">
                @if ($subclass->profileData())
                    <div class="col-lg-6">
                        @foreach($subclass->profileData() as $key => $value)
                            @switch($value['type'])
                                @case('textarea')
                                @case('textarea_extended')
                                    <div class="col-12 mb-4">
                                        <x-mfw::textarea name="profile[{{$key}}]"
                                                    :className="$value['type'] .' '.($value['class']??'') "
                                                    value="{!! $error ? old('profile.'.$key) : (isset($account->profile->{$key}) ? $account->profile->{$key} : '') !!}" label="{{$value['label']}}"/>
                                    </div>
                                    @break
                                @default

                                    <div class="{{ $value['class'] ?? 'col-12' }} mb-4">
                                        <x-mfw::input name="profile[{{$key}}]" :required="$value['required']" value="{{  $error ? old('profile.'.$key) : (isset($account->profile->{$key}) ? $account->profile->{$key} : '') }}" label="{{$value['label']}}"/>
                                    </div>

                            @endswitch
                        @endforeach
                    </div>
                @endif
                <div class="col-lg-6">
                    <h4>Photo</h4>

                    <x-mediaclass::uploadable :model="$account"
                                              :settings="['group'=>'avatar']"
                                              size="small"
                                              :limit="1"
                                              :description="false"
                                              label="Ajouter une photo de profil"/>
                </div>
            </div>
        </fieldset>

    @endif
@empty

@endforelse
