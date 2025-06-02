<div class="card border">
    <div class="card-header border-bottom text-uppercase">
        <h5 class="mb-0 text-light-emphasis">{!! __('front/account.credentials_info') !!}</h5>
    </div>
    <div class="card-body" x-data="{
        'editPassword': {{ (int)session('registration_temp_password') }},
    }" x-effect="document.getElementById('password_change').value = editPassword">

        <div x-cloak x-show="">
            <div class="d-flex gap-3 mb-4 border-bottom">
                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="edit_password"
                           x-model="editPassword"
                           id="edit_password_no"
                           value="0"
                    >
                    <label class="form-check-label" for="edit_password_no">
                        {{ __('auth.keep_password') }}
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="edit_password"
                           x-model="editPassword"
                           id="edit_password_yes"
                           value="1"
                    >
                    <label class="form-check-label" for="edit_password_yes">
                        {{ __('auth.change_password') }}
                    </label>
                </div>
            </div>

            <input type="hidden" name="password_change" id="password_change" />
        </div>


        <div x-cloak x-show="editPassword == 0">
            <p>
                {{ __('auth.login_info_will_be_keeped') }}
            </p>
        </div>

        @if (session('registration_temp_password'))
            <p class="text-dark">{{  __('front/register.we_have_made_a_password_for_you', ['password' => session('registration_temp_password')]) }}</p>
        @endif

        <div x-cloak x-show="editPassword == 1">
            <div class="row mb-3">
                <label for="input_pass"
                       class="col-md-4 col-form-label text-start text-nowrap">{{__('front/account.labels.password')}}</label>
                <div class="col-md-8">
                    <input type="password"
                           name="password"
                           value="{{ session('registration_temp_password') }}"
                           class="form-control rounded-0"
                           id="input_pass">
                    <div class="form-text">{{__('front/account.labels.password_at_least_x_chars')}}</div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="input_pass_confirm"
                       class="col-md-4 col-form-label text-start text-nowrap">{{__('front/account.labels.password_confirmation')}}</label>
                <div class="col-md-8">
                    <input type="password"
                           name="password_confirmation"
                           value="{{ session('registration_temp_password') }}"
                           class="form-control rounded-0"
                           id="input_pass_confirm">

                </div>
            </div>
        </div>
    </div>
</div>
