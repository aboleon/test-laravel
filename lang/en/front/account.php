<?php

return [
    'individual_account' => 'Personal account',
    'manager_account' => 'Group Manager',
    'my_personal_info' => 'My personal info',
    'mails_title' => 'Mails',
    'main_mail' => 'Main email / login',
    'phones_title' => 'Phones',
    'phone_legend' => "(blue background = main phone)",
    "labels" => [
        "billing_badge" => "billing",
        "address_number" => "N°",
        "address_street" => "Street",
        "address_zip_code" => "Zip Code",
        "address_city" => "City",
        "address_country" => "Country",
        "address_choose_a_country" => "--- Choose a country ---",
        "address_cedex" => "Cedex",
        "domain" => <<<EOT
          Domain <span class="text-danger">*</span>
        EOT,
        "profession" => <<<EOT
          Job title <span class="text-danger">*</span>
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
        "password" => "Password",
        "password_at_least_x_chars" => "At least 8 characters",
        "password_confirmation" => "Please enter your password again",

        "new_password" => "New password",
        "confirm_new_password" => "Confirm New password",
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
        "participation_type" => <<<EOT
          Participation <span class="text-nowrap">type <span class="text-danger">*</span></span>
        EOT,
        "savant_society" => <<<EOT
          Scientific <span class="text-nowrap">organization</span>
        EOT,
        "establishment" => <<<EOT
          Establishment
        EOT,
        "main_phone" => <<<EOT
          Main <span class="text-nowrap">phone</span>
        EOT,
        "rpps" => "RPPS",
        "rpps_notice" => "For a registration via a Grant, the RPPS number is mandatory for French doctors. To be able to register an RPPS, you must have an address in France.",
        "birth_date" => <<<EOT
          Date of birth <span class="text-danger">*</span>
         EOT,
        "photo" => "Photo",
        "administrative_area_level_1" => "County",
        "administrative_area_level_2" => "State",
        "email" => "Email",
        "name" => "Document title",
        "loyalty_card_title" => "Card title",
        "company" => "Company",
        "address_complement" => "Address complement",
        "billing" => "Billing address",
        "text_address" => "Full address",
        "serial" => "Number",
        "expires_on" => "Expires on",
        "issued_on" => "Issued on",
    ],
    "is_main_address" => "Is main",
    "check_your_info_message" => <<<EOT
            If you wish to benefit from a support, you will have to fill in your RPPS, your
            domain (main activity), your Address, Zip Code, City and Country.
        EOT,
    "general_info" => "General information",
    "credentials_info" => "Credentials information",
    "other_emails" => "Other emails",
    "other_phones" => "Other phones",
    "identity_cards" => "Identity cards",
    "loyalty_cards" => "Loyalty cards",
    "documents" => "Documents",
    "expires_on" => "Expires on",
    "issued_on" => "Issued on",
    "address" => "Address",
    "addresses" => "Addresses",
    'validation' => [
        'participation_type' => 'The participation type is required.',
        'password' => 'The password is required.',
        'domain' => 'The domain is required.',
        'genre' => 'The gender is required.',
        'birth' => 'The birth date is required.',
        'first_name' => 'The first name is required.',
        'last_name' => 'The last name is required.',
        'lang' => 'The language is required.',
        'profession_id' => 'The profession is required.',
        'function' => 'The function is required.',
        'email' => 'The email is required.',
        'email_format' => 'The email format is invalid.',
        'phone' => 'The phone is required.',
        'name' => "The card title is required.",
        'serial' => "The number is required.",
        'text_address' => "The address is required.",
        "at_least_one_address" => "At least one address is required.",
    ],
    'update_success' => 'Your information has been successfully updated.',
    'add_an_email' => 'Add an email',
    'update_an_email' => 'Update the email',
    'email_already_exists' => "The email already exists.",
    'phone_added' => "The phone has been added.",
    'phone_updated' => "The phone has been updated.",
    'add_a_phone' => 'Add a phone',
    'update_a_phone' => 'Update a phone',
    'change_photo' => "Change photo",
    'name' => "Name",
    'indicatif' => "Telephone code",
    'num_principal' => "This is my main number",
    'name_placeholder' => "Home, mobile, ...",
    'phone_placeholder' => "Phone",
    'add_an_identity_card' => 'Add an identity card',
    'update_an_identity_card' => 'Update an identity card',
    'identity_card_added' => "The identity card has been added.",
    'identity_card_updated' => "The identity card has been updated.",
    'add_a_loyalty_card' => 'Add a loyalty card',
    'update_a_loyalty_card' => 'Update a loyalty card',
    'loyalty_card_added' => "The loyalty card has been added.",
    'loyalty_card_updated' => "The loyalty card has been updated.",
    'add_an_address' => 'Add an address',
    'update_an_address' => 'Update an address',
    'address_added' => "The address has been added.",
    'address_updated' => "The address has been updated.",
    'modifying_account_by_manager_alert' => "The data for :account is linked to their online account. If you modify it, their account will be changed.<br>It is your responsibility to verify the data with :account before making any modifications.<br>By modifying this data, you confirm that you have received permission from :account to do so.",
    'has_no_valid_address' => "Warning: you have no valid billing address or it is incomplete.<br></br>Please <a href=':url?redirectToCart' class='alert-link text-decoration-underline'>intervene</a> in order to pursue.",
    'domain_warning' => "* If you don't see your domain in the list, please select Other"
];
