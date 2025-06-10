<?php

return [
    'validation' => [
        "participation_type_required" => "You did not mention your type of participation",
        "participation_type_unauthorized" => "This type of participation is not authorized for this event",
        "domain_required" => "You have not specified your domain",
        "profession_required" => "You have not specified your profession",
        "function_required" => "You have not specified your function",
        "genre_required" => "You have not specified your gender",
        "first_name_required" => "You have not specified your first name",
        "last_name_required" => "You have not specified your last name",
        "language_id_required" => "You have not specified your language",
        "email_required" => "You have not specified your email",
        "email_already_exists" => "This email is already in use",
        "email_format" => "Your email is incorrectly formatted",
        "email_confirmed" => "You have not confirmed your email",
        "password_required" => "You have not specified your password",
        "password_confirmed" => "You have not confirmed your password",
        "address_required" => "You have not specified your address",
        "zipcode_required" => "You have not specified your postal code",
        "city_required" => "You have not specified your city",
        "country_code_required" => "You have not specified your country",
    ],
    "labels" => [
        "your_email_address" => "Your email address",
        "your_email_address_placeholder" => "email address",
        "participation_type" => <<<EOT
          Type of <span class="text-nowrap">participation <span class="text-danger">*</span></span>
        EOT,
        "domain" => <<<EOT
          Domain <span class="text-danger">*</span>
        EOT,
        "profession" => <<<EOT
          Profession <span class="text-danger">*</span>
        EOT,
        "fonction" => <<<EOT
          Specialty <span class="text-danger">*</span>
        EOT,
        "title" => "Title",
        "genre" => <<<EOT
          Gender <span class="text-danger">*</span>
        EOT,
        "first_name" => <<<EOT
          First <span class="text-nowrap">name <span class="text-danger">*</span></span>
        EOT,
        "last_name" => <<<EOT
          Last <span class="text-nowrap">name <span class="text-danger">*</span></span>
        EOT,
        "passport_first_name" => <<<EOT
          Passport first <span class="text-nowrap">name</span>
        EOT,
        "passport_last_name" => <<<EOT
          Passport last <span class="text-nowrap">name</span>
        EOT,
        "language" => <<<EOT
          Language <span class="text-danger">*</span>
        EOT,
        "main_mail" => <<<EOT
          Main <span class="text-nowrap">email <span class="text-danger">*</span></span>
        EOT,
        "main_mail_confirmation" => <<<EOT
          Email <span class="text-nowrap">confirmation <span class="text-danger">*</span></span>
        EOT,
        "password" => <<<EOT
          Password
        EOT,
        "new_password" => "New password",
        "confirm_new_password" => "Confirm New password",
        "password_confirmation" => <<<EOT
          Password <span class="text-nowrap">confirmation <span class="text-danger">*</span></span>
        EOT,
        "company" => "Company name",
        "address" => <<<EOT
          Address <span class="text-danger">*</span>
        EOT,
        "postal_code" => <<<EOT
          ZIP <span class="text-nowrap">Code <span class="text-danger">*</span></span>
        EOT,
        "city" => <<<EOT
          City <span class="text-danger">*</span>
        EOT,
        "country_code" => <<<EOT
          Country <span class="text-danger">*</span>
        EOT,
    ],
    'mail_confirm_notice' => <<<EOT
        NB: You will receive a confirmation letter by email. If you cannot find it, the email is probably in your spam, promotions or unwanted email folder.
        To receive our communications, please make sure to approve the email sender.
    EOT,
    'create_an_account' => 'Create my account',
    "create_my_account" => "Create my account",
    'account_creation' => 'Create my account',
    'cancel' => 'Cancel',
    'mandatory_fields' => 'mandatory fields',
    'user_not_found_with_given_email' => 'User not found with the given email',
    "error_user_not_found" => "An error occurred, the user was not found.",
    'account_already_created_with_given_email' => "An account has already been created with the given email",
    'error_registered_user_not_in_session' => "An error occurred, you must first click the link in your email.",
    'error_registered_user_not_found' => "An error occurred, the user was not found.",
    "i_accept_conditions" => "By creating my account, I accept",
    "the_conditions_of_use" => "the terms of use",
    "you_must_accept_conditions_to_continue" => "You must accept the terms to continue",
    "i_want_to_subscribe_to_the_newsletter" => "I want to receive newsletters from the event's partners",
    "i_want_to_subscribe_to_sms_notifications" => "I want to receive sms related to the organization of the event",
    "an_error_occurred" => "An error occurred.",
    "if_error_persists_contact_support" => "If this error persists, please contact support.",
    "go_back_to_home_page" => "Go back to the home page.",
    "next" => "Next",
    "previous" => "Previous",
    "congrats_you_are_registered" => "Congratulations, you are registered",
    "go_to_event" => "Go to the event",
    "email_already_exist_for_this_congress" => "This email address is linked to an existing account for this congress.",
    "forgotten_password" => "Forgotten password",
    "connect" => "Login",
    "email_sent_to" => "An email has been sent to the address :mail",
    "mail_expiration_notice" => <<<EOT
        You have :time to check your emails and validate the creation of your account.
        <br>
        If you do not receive the email, please check your spam folder.
    EOT,
    'mail_validation_notice' => "You must validate the creation of your account by following the validation link within the e-mail.<br>If you do not receive the email, please check your spam folder. If you don't receive an e-email, contact-us at :email with subject : Problem creating my account",
    'instance_not_found' => "We couldn't find your initial subscrioption demand. <a href=':route'>Please, make a new one.</a>",
    "email_not_received" => "You have not received an email?",
    "resend" => "Resend",
    "another_email_was_sent" => "Another email has just been sent.",
    "thanks_for_validating_your_email" => "Thank you for validating your email address.",
    'you_have_already_validated' => "You have already validated your subscription demand. Please finish the subscription process.",
    'you_have_already_registered' => "You have already registered with us. If you have forgotten your password, please go to Reset password.",
    "complete_the_registration_process" => "Complete the registration process",
    "confirm_link_expired" => "The confirmation link has expired.",
    "token_not_found" => "The provided token is not valid.",
    "send_again" => "Send again",
    "rgpd_title" => "Privacy policy",
    "rgpd_text" => <<<EOT
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. A alias amet architecto consequatur corporis
        distinctio eum fugiat, illum ipsum labore nesciunt omnis placeat praesentium temporibus voluptatem. Ad
        consequatur nam repellat?
    EOT,
    'we_have_made_a_password_for_you' => "We have generated a password for you - :password. You can keep it or change it with your own here below.",
    'you_have_account_but_not_for_this_event' => "Your account exists, but it is not yet linked to this event.<br>You have to click on the one of buttons here below depending on your situation in order to continue.",
    'this_email_is_not_allowed' => "This e-mail address is not eligible for account creation."
];
