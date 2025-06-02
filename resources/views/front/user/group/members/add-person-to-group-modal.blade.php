<div class="modal fade"
     id="addPersonToGroupModal"
     data-ajax="{{route('ajax')}}"
     tabindex="-1"
     aria-labelledby="addPersonToGroupModalLabel"
     aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5"
                    id="addPersonToGroupModalLabel">{{__('front/groups.add_modal_add_person_to_group')}}</h1>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="{{ __('ui.close') }}"></button>
            </div>
            <div class="messages container mt-3"></div>

            <div class="modal-body">
                <div class="row mb-3 align-items-center">
                    <label for="i-email"
                           class="col-form-label col-2">{{__('front/groups.add_modal_email')}}</label>
                    <div class="col-7">
                        <input type="email"
                               name="email"
                               class="form-control"
                               id="i-email"
                               placeholder="name@example.com">
                    </div>
                    <div class="col-3">
                        <button class="btn btn-sm btn-primary mb-0 btn-check-email">
                            {{__('front/groups.add_modal_check')}}
                            <div style="display: none;"
                                 class="check-mail-spinner spinner-border spinner-border-sm"
                                 role="status">
                                <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                            </div>

                        </button>
                    </div>
                </div>

                <div class="if-email-notfound" style="display: none;">
                    <p>
                        {{ __('front/ui.has_no_account') }}
                    </p>
                    <p>
                        {{ __('front/ui.following_is_needed') }}
                    </p>
                    <ul>
                        <li>{{ __('ui.domain') }}</li>
                        <li>{{ __('front/ui.gender') }}</li>
                        <li>{{ __('front/ui.names') }}</li>
                        <li>{{ __('account.birth') }}</li>
                        <li>{{ __('front/ui.languages') }}</li>
                        <li>{{ __('account.profile.profession_id') }}</li>
                        <li>{{ __('account.profile.function') }}</li>
                        <li>{{ __('front/account.labels.text_address') }}</li>
                        <li>{{ __('account.password') }}</li>
                    </ul>
                    <p>
                        {{ __('front/ui.continue_adding_member') }}
                    </p>
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">{{ __('ui.no') }}</button>
                        <button class="action-create-group-member-from-email btn btn-sm btn-primary">
                            {{ __('ui.yes') }}
                            <div style="display: none;"
                                 class="create-group-member-from-email-spinner spinner-border spinner-border-sm"
                                 role="status">
                                <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                            </div>
                        </button>
                    </div>


                </div>
                <div class="if-email-verified" style="display: none;">
                    <div class="fs-5 ps-2 text-bg-info">
                        {{__('front/groups.add_modal_is_it_this_person')}}
                    </div>
                    <div class="border p-3 d-flex justify-content-between gap-1">
                        <div>
                            <div class="text-fullname fs-5"></div>
                            <div class="text-email"></div>
                            <div class="text-location"></div>
                        </div>
                        <div>
                            <div class="if-has-participation-type" style="display: none;">
                                <div class="text-participation-type"></div>
                            </div>
                            <div class="ifnot-has-participation-type w-150px"
                                 style="display: none;">
                                <div class="mb-3 row">
                                    <label for="s-type-participation"
                                           class="form-label ps-0">{{__('front/groups.add_modal_participation_type')}}</label>
                                    <x-select-participation-type
                                            name="participation_type"
                                            id="s-type-participation"
                                            class="form-select form-select-sm"
                                            :excludeGroups="['orator']"
                                    />
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-3">
                        <button type="button"
                                class="btn btn-sm btn-secondary me-auto close-email-verified">
                            {{__('front/groups.add_modal_cancel')}}
                        </button>
                        <div>
{{--                            <button class="btn btn-sm btn-primary">{{__('front/groups.add_modal_create_new_person')}}--}}
{{--                            </button>--}}
                            <button class="btn btn-sm btn-success associate-person-to-group">
                                {{__('front/groups.add_modal_yes_it_is_this_person')}}
                                <div style="display: none;"
                                     class="attach-person-spinner spinner-border spinner-border-sm"
                                     role="status">
                                    <span class="visually-hidden">{{ __('front/ui.loading') }}</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">{{__('front/groups.add_modal_close')}}
                </button>
            </div>
            <input type="hidden" id="participation_type_id" />
        </div>
    </div>
</div>
