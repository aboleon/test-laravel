<?php

return [
    'individual_account' => 'Compte individuel',
    'manager_account' => 'Chef de groupe',
    'my_personal_info' => 'Mes informations personnelles',
    'mails_title' => 'Mails',
    'phones_title' => 'Téléphones',
    'main_mail' => 'Adresse email principale / de connexion',
    'phone_legend' => "(fond bleu = téléphone principal)",
    "labels" => [
        "billing_badge" => "facturation",
        "address_number" => "N°",
        "address_street" => "Rue",
        "address_zip_code" => "Code postal",
        "address_city" => "Ville",
        "address_country" => "Pays",
        "address_choose_a_country" => "--- Choisissez un pays ---",
        "address_cedex" => "Cedex",
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

        "password" => "Indiquez ici votre mot de passe",
        "password_at_least_x_chars" => "Au minimum 8 caractères",
        "password_confirmation" => "Veuillez saisir à nouveau votre mot de passe",

        "new_password" => "Nouveau mot de passe",
        "confirm_new_password" => "Confirmation nouveau mot de passe",
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
        "participation_type" => <<<EOT
          Type de participation :event: <span class="text-danger">*</span>
        EOT,
        "savant_society" => <<<EOT
          Société <span class="text-nowrap">savante</span>
        EOT,
        "establishment" => <<<EOT
          Établissement
        EOT,
        "main_phone" => <<<EOT
          Téléphone <span class="text-nowrap">principal</span>
        EOT,
        "rpps" => "RPPS",
        "rpps_notice" => "Pour une inscription via un Grant, le numéro RPPS est obligatoire pour les médecins français. Pour pouvoir enregistrer un RPPS il faut obligatoirement disposer d'une adresse en France.",
        "birth_date" => <<<EOT
          Date de naissance <span class="text-danger">*</span>
         EOT,
        "photo" => "Photo",
        "administrative_area_level_1" => "Département",
        "administrative_area_level_2" => "Région",
        "email" => "Email",
        "name" => "Titre du document",
        "loyalty_card_title" => "Titre de la carte",
        "company" => "Raison sociale",
        "address_complement" => "Complément d'adresse",
        "billing" => "Adresse de facturation",
        "text_address" => "Adresse complète",
        "serial" => "Numéro",
        "expires_on" => "Expire le",
        "issued_on" => "Délivrée le",
    ],
    "is_main_address" => "Est principal",
    "check_your_info_message" => <<<EOT
            Si vous souhaitez bénéficier d'une prise en charge il faudra renseigner votre RPPS, votre
            domaine (activité principale), votre Adresse, CP, Ville et Pays.
        EOT,
    "general_info" => "Informations générales",
    "credentials_info" => "Informations de connexion",
    "other_emails" => "Autres emails",
    "other_phones" => "Autres téléphones",
    "identity_cards" => "Pièces d'identité",
    "loyalty_cards" => "Cartes de fidélité voyage",
    "documents" => "Documents",
    "expires_on" => "Expire le",
    "issued_on" => "Délivré le",
    "address" => "Adresse",
    "addresses" => "Adresses",
    'validation' => [
        'participation_type' => 'Le type de participation est obligatoire.',
        'password' => "Le mot de passe est obligatoire.",
        'domain' => 'Le domaine est obligatoire.',
        'genre' => 'Le genre est obligatoire.',
        'birth' => 'La date de naissance est obligatoire.',
        'first_name' => 'Le prénom est obligatoire.',
        'last_name' => 'Le nom est obligatoire.',
        'language_id' => 'La langue est obligatoire.',
        'profession_id' => 'La profession est obligatoire.',
        'function' => 'La fonction est obligatoire.',
        'phone' => 'Le téléphone est obligatoire.',
        'email' => "L'email est obligatoire.",
        'email_format' => "Le format de l'email est invalide.",
        'name' => "Le nom de la carte est obligatoire.",
        'serial' => "Le numéro est obligatoire.",
        'text_address' => "L'adresse est obligatoire.",
        "at_least_one_address" => "Vous devez renseigner au moins une adresse.",
    ],
    'update_success' => 'Vos informations ont été mises à jour avec succès.',
    'add_an_email' => 'Ajouter un email',
    'update_an_email' => "Modifier l'email",
    'email_already_exists' => "L'email existe déjà.",
    'phone_added' => "Le téléphone a été ajouté.",
    'phone_updated' => "Le téléphone a été mis à jour.",
    'add_a_phone' => 'Ajouter un téléphone',
    'update_a_phone' => 'Modifier un téléphone',
    'change_photo' => 'Modifier la photo',
    'name' => "Nom",
    'indicatif' => "Indicatif",
    'num_principal' => "Il s'agit de mon numéro principal",
    'name_placeholder' => "Domicile, mobile, ...",
    'phone_placeholder' => "Téléphone",
    'add_an_identity_card' => "Ajouter une pièce d'identité",
    'update_an_identity_card' => "Modifier la pièce d'identité",
    'identity_card_added' => "La pièce d'identité a été ajoutée.",
    'identity_card_updated' => "La pièce d'identité a été mise à jour.",
    'add_a_loyalty_card' => 'Ajouter une carte de fidélité',
    'update_a_loyalty_card' => 'Modifier une carte de fidélité voyage',
    'loyalty_card_added' => "La carte de fidélité a été ajoutée.",
    'loyalty_card_updated' => "La carte de fidélité a été mise à jour.",
    'add_an_address' => 'Ajouter une adresse',
    'update_an_address' => 'Modifier une adresse',
    'address_added' => "L'adresse a été ajoutée.",
    'address_updated' => "L'adresse a été mise à jour.",
    'modifying_account_by_manager_alert' => "Les données de :account sont liées à son compte en ligne. Si vous les modifiez, cela modifiera son compte.<br>Il vous appartient de vérifier les données avec :account avant de les modifier.<br>Vous confirmez, en modifiant ces données, avoir reçu l'autorisation de :account pour le faire.",
    'has_no_valid_address' => "Attention, vous n'avez pas de coordonnées de facturation ou bien elles sont incomplètes.<br>Veuillez <a href=':url?redirectToCart' class='alert-link text-decoration-underline'>les renseigner ou corriger</a> avant de continuer.",
    'domain_warning' => '* Si vous ne trouvez pas votre domaine, veuillez sélectionner “Autre”'
];
