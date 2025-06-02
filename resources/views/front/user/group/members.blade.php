<x-front-logged-in-group-manager-layout :event="$event">

    <div class="container d-flex justify-content-end">
        <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addPersonToGroupModal"
        ><i class="bi bi-plus-circle"></i> {{__('front/groups.add_a_person')}}
        </button>
    </div>
    <div class="container front-datatable datatable-members mt-5 datatable-not-clickable">
        {!! $dataTable->table()  !!}
    </div>
    @include('lib.datatable')
    @push('js')
        {{ $dataTable->scripts() }}
    @endpush


    @push("modals")
        @include("front.user.group.members.add-person-to-group-modal")
        <x-confirm-modal
                id="dissociateConfirmModal"
                title="{{__('front/groups.confirm_delete_person_btn')}}"
                text="{{__('front/groups.confirm_delete_person')}}"
                confirm-btn-text="{{__('front/groups.confirm_delete_person_btn')}}"
        />
    @endpush

    @push("js")
        <script>
            $(document).ready(function() {

                let event_group_contact_id = null;

                const jAddPersonModal = $('#addPersonToGroupModal');
                const jDatatableContext = $('.datatable-members');
                const jCheckMailSpinner = jAddPersonModal.find('.check-mail-spinner');
                const jAttachPersonSpinner = jAddPersonModal.find('.attach-person-spinner');
                const jCreateGroupMemberFromEmail = jAddPersonModal.find('.create-group-member-from-email-spinner');
                const jIfEmailVerified = jAddPersonModal.find('.if-email-verified');
                const jIfEmailNotFound = jAddPersonModal.find('.if-email-notfound');
                const jIfParticipationType = jAddPersonModal.find('.if-has-participation-type');
                const jIfNotParticipationType = jAddPersonModal.find('.ifnot-has-participation-type');
                let userInfo = null;
                let useParticipationType = false;

                function closeModal() {
                    jIfEmailVerified.hide();
                    jIfEmailNotFound.hide();
                }

                function whenUserEmailFound(userInfo) {
                    jIfEmailNotFound.hide();
                    jIfEmailVerified.show();
                    jIfEmailVerified.find('.text-fullname').text(userInfo.first_name + ' ' + userInfo.last_name);
                    jIfEmailVerified.find('.text-email').text(userInfo.email);
                    jIfEmailVerified.find('.text-location').text(userInfo.location);
                    if (userInfo.participation_type) {
                        jIfParticipationType.show();
                        jIfNotParticipationType.hide();
                        jIfEmailVerified.find('.text-participation-type').text(userInfo.participation_type);
                        useParticipationType = false;
                    } else {
                        jIfParticipationType.hide();
                        jIfNotParticipationType.show();
                        useParticipationType = true;
                    }
                }

                jAddPersonModal.on('click', function(e) {
                    let jTarget = $(e.target);

                    if (jTarget.hasClass('btn-check-email')) {
                        let email = jAddPersonModal.find('input[name=email]').val();
                        let action = "action=getUserInfoByEventEmail&event_id={{$event->id}}&email=" + email;
                        ajax(action, jAddPersonModal, {
                            spinner: jCheckMailSpinner,
                            successHandler: function(r) {
                                userInfo = r.user;
                                if ('notfound' === userInfo) {
                                    jIfEmailNotFound.show();
                                    jIfEmailVerified.hide();
                                    return;
                                }
                                whenUserEmailFound(userInfo);
                                return true;
                            },
                            errorHandler: function(r) {
                                jIfEmailVerified.hide();
                                return true;
                            },
                        });
                        return false;
                    } else if (jTarget.hasClass('associate-person-to-group')) {
                        let action = 'action=associateUserToEventGroup&user_id=' + userInfo.id + "&event_group_id={{$eventGroup->id}}";
                        if (useParticipationType) {
                            let participationType = jAddPersonModal.find('select[name=participation_type]').val();
                            action += '&participation_type_id=' + participationType;
                        }

                        ajax(action, jAddPersonModal, {
                            spinner: jAttachPersonSpinner,
                            successHandler: function(r) {
                                jIfEmailVerified.hide();
                                jAddPersonModal.find('input[name=email]').val('');
                                jAddPersonModal.find('select[name=participation_type]').val('');
                                $('.dt').DataTable().ajax.reload();
                                jAddPersonModal.modal('hide');
                                setTimeout(function() {
                                    jAddPersonModal.find('.messages').empty();
                                }, 1000);
                                return true;
                            },
                        });
                        return false;
                    } else if (jTarget.hasClass('close-email-verified')) {
                        jIfEmailNotFound.hide();
                        jIfEmailVerified.hide();
                        return false;
                    } else if (jTarget.hasClass('action-create-group-member-from-email')) {
                        let email = jAddPersonModal.find('input[name=email]').val();
                        let action = 'action=createGroupMemberFromMail&email=' + email + "&event_id={{$event->id}}&event_group_id={{$eventGroup->id}}";
                        ajax(action, jAddPersonModal, {
                            spinner: jCreateGroupMemberFromEmail,
                            successHandler: function(r) {
                                if (r.userInfo) {
                                    whenUserEmailFound(r.userInfo);
                                } else {
                                    window.location.href = r.url;
                                }
                                return true;
                            },
                        });
                        return false;
                    }
                });

                jDatatableContext.on('click', '.action-dissociate-member-from-group', function(e) {
                    event_group_contact_id = $(this).data('id');
                    let jConfirmModal = $('#dissociateConfirmModal');
                    jConfirmModal.modal('show');
                    jConfirmModal.find('.messages').empty();
                    jConfirmModal.find('.action-confirm').off().on('click', function() {
                        let action = 'action=dissociateUserFromMyEventGroup&event_group_contact_id=' + event_group_contact_id;
                        ajax(action, jConfirmModal, {
                            spinner: jConfirmModal.find('.spinner'),
                            successHandler: function(r) {
                                jConfirmModal.modal('hide');
                                $('.dt').DataTable().ajax.reload();
                                return true;
                            },
                        });
                    });
                    return false;
                });

                jAddPersonModal.on('hidden.bs.modal', function() {
                    closeModal();
                });

            });
        </script>
    @endpush

</x-front-logged-in-group-manager-layout>
