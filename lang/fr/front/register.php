<?php

return [
    'validation' => [
        "participation_type_required" => "Vous n'avez pas précisé votre type de participation",
        "participation_type_unauthorized" => "Ce type de participation n'est pas autorisé pour cet événement",
        "domain_required" => "Vous n'avez pas précisé votre domaine",
        "profession_required" => "Vous n'avez pas précisé votre fonction",
        "function_required" => "Vous n'avez pas précisé votre fonction",
        "genre_required" => "Vous n'avez pas précisé votre genre",
        "first_name_required" => "Vous n'avez pas précisé votre prénom",
        "last_name_required" => "Vous n'avez pas précisé votre nom",
        "language_id_required" => "Vous n'avez pas précisé votre langue",
        "email_required" => "Vous n'avez pas précisé votre email",
        "email_already_exists" => "Cet email est déjà utilisé",
        "email_format" => "Votre email est mal formaté",
        "email_confirmed" => "Vous n'avez pas confirmé votre email",
        "password_required" => "Vous n'avez pas précisé votre mot de passe",
        "password_confirmed" => "Vous n'avez pas confirmé votre mot de passe",
        "address_required" => "Vous n'avez pas précisé votre adresse",
        "zipcode_required" => "Vous n'avez pas précisé votre code postal",
        "city_required" => "Vous n'avez pas précisé votre ville",
        "country_code_required" => "Vous n'avez pas précisé votre pays",
    ],
    "labels" => [
        "your_email_address" => "Votre adresse mail",
        "your_email_address_placeholder" => "adresse mail",
        "participation_type" => <<<EOT
          Type de <span class="text-nowrap">participation <span class="text-danger">*</span></span>
        EOT,
        "domain" => <<<EOT
          Domaine <span class="text-danger">*</span>
        EOT,
        "profession" => <<<EOT
          Profession <span class="text-danger">*</span>
        EOT,
        "fonction" => <<<EOT
          Fonction <span class="text-danger">*</span>
        EOT,
        "title" => "Titre",
        "genre" => <<<EOT
          Genre <span class="text-danger">*</span>
        EOT,
        "first_name" => <<<EOT
          Prénom <span class="text-danger">*</span>
        EOT,
        "last_name" => <<<EOT
          Nom <span class="text-danger">*</span>
        EOT,
        "passport_first_name" => <<<EOT
          Prénom <span class="text-nowrap">passeport</span>
        EOT,
        "passport_last_name" => <<<EOT
          Nom <span class="text-nowrap">passeport</span>
        EOT,
        "language" => <<<EOT
          Langue <span class="text-danger">*</span>
        EOT,
        "main_mail" => <<<EOT
          Mail <span class="text-nowrap">principal <span class="text-danger">*</span></span>
        EOT,
        "main_mail_confirmation" => <<<EOT
          Confirmation <span class="text-nowrap">email <span class="text-danger">*</span></span>
        EOT,
        "password" => <<<EOT
          Mot de passe
        EOT,
        "new_password" => "Nouveau mot de passe",
        "confirm_new_password" => "Confirmation nouveau mot de passe",
        "password_confirmation" => <<<EOT
          Confirmation mot de <span class="text-nowrap">passe <span class="text-danger">*</span></span>
        EOT,
        "company" => "Raison sociale",
        "address" => <<<EOT
          Adresse <span class="text-danger">*</span>
        EOT,
        "postal_code" => <<<EOT
          Code <span class="text-nowrap">postal <span class="text-danger">*</span></span>
        EOT,
        "city" => <<<EOT
          Ville <span class="text-danger">*</span>
        EOT,
        "country_code" => <<<EOT
          Pays <span class="text-danger">*</span>
        EOT,
    ],
    'mail_confirm_notice' => <<<EOT
        NB: Une confirmation vous sera envoyée automatiquement, si vous ne la recevez pas
        surveillez votre boîte « courrier indésirable » et autorisez l’expéditeur.
    EOT,
    'create_an_account' => 'Créer un compte',
    'create_my_account' => 'Créer mon compte',
    'account_creation' => 'Création de compte',
    'cancel' => 'Annuler',
    'mandatory_fields' => 'champs obligatoires',
    'user_not_found_with_given_email' => "Aucun utilisateur trouvé avec l'email donné",
    "error_user_not_found" => "Une erreur est survenue, l'utilisateur n'a pas été trouvé.",
    'account_already_created_with_given_email' => "Un compte a déjà été créé avec l'email donné",
    'error_registered_user_not_in_session' => "Une erreur est survenue, vous devez d'abord valider votre email.",
    'error_registered_user_not_found' => "Une erreur est survenue, l'utilisateur n'a pas été trouvé.",
    "i_accept_conditions" => "En créant mon compte, j'accepte",
    "the_conditions_of_use" => "la politique de confidentialité",
    "you_must_accept_conditions_to_continue" => "Vous devez accepter les conditions pour continuer",
    "i_want_to_subscribe_to_the_newsletter" => "Je souhaite recevoir des newsletters des partenaires de l'événement",
    "i_want_to_subscribe_to_sms_notifications" => "Je souhaite recevoir des sms liés à l'organisation de l'évènement",
    "an_error_occurred" => "Une erreur est survenue.",
    "if_error_persists_contact_support" => "Si cette erreur persiste, merci de contacter le support.",
    "go_back_to_home_page" => "Retourner à la page d'accueil.",
    "next" => "Suivant",
    "previous" => "Précédent",
    "congrats_you_are_registered" => "Félicitations, votre compte est désormais créé.",
    "go_to_event" => "Accéder à l'événement",
    "email_already_exist_for_this_congress" => "Cette adresse email est rattachée à un compte existant pour ce congrès.",
    "forgotten_password" => "Mot de passe oublié ?",
    "connect" => "Se connecter",
    "email_sent_to" => "Un mail a été envoyé à l'adresse :mail",
    "mail_expiration_notice" => <<<EOT
        Vous avez :time pour vérifier vos mails et valider la création de votre compte.
        <br>
        Pensez aussi à vérifier vos courriers indésirables.
    EOT,
    'mail_validation_notice' => "Vous devez valider la création de compte depuis le lien contenu dans l'email.<br>
        Pensez aussi à vérifier vos courriers indésirables. Si vous n'avez toujours pas reçu de mail, contactez-nous sur :email avec objet : Problème création de compte",
    'instance_not_found' => "Nous n'avons pas pu retrouver votre demande initiale. <a href=':route'>Veuillez recommencer l'inscription.</a>",
    "email_not_received" => "Vous n'avez pas reçu d'email ?",
    "resend" => "Renvoyer",
    "another_email_was_sent" => "Un autre email vient d'être envoyé.",
    "thanks_for_validating_your_email" => "Merci d'avoir validé votre adresse email.",
    'you_have_already_validated' => "Vous avez déjà validé cette demande. <a href=':route' class='btn btn-sm btn-primary my-0 ms-5'>Veuillez finaliser le processus d'inscription.</a>",
    'you_have_already_registered' => "Vous avez déjà complété la démarche intégrale de souscription à cet évènement. Si vous avez oubliez votre mot de passe, demandez en un nouveau via le formulaire Mot de passé oublié.",
    "complete_the_registration_process" => "Finaliser la création de mon compte",
    "confirm_link_expired" => "Le lien de confirmation a expiré.",
    "token_not_found" => "Le token fourni n'est pas valide.",
    "send_again" => "Renvoyer à nouveau",
    "rgpd_title" => "Politique de confidentialité",
    "rgpd_text" => <<<EOT
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. A alias amet architecto consequatur corporis
        distinctio eum fugiat, illum ipsum labore nesciunt omnis placeat praesentium temporibus voluptatem. Ad
        consequatur nam repellat?
    EOT,
    'we_have_made_a_password_for_you' => "Nous avons généré un mot de passe pour votre compte - :password. Vous pouvez le garder ou le changer ci-dessous.",
    'you_have_account_but_not_for_this_event' => "Votre compte existe, mais n'est pas rattaché à cet événement.<br>Vous devez cliquer sur l’un des boutons ci-dessous en fonction de votre situation pour pouvoir continuer.",
    'this_email_is_not_allowed' => "Cette adresse e-mail n'est pas éligible pour la création d'un compte d'accès."
];
