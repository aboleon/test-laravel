<?php

return [
    'login_to_my_account' => 'Connexion à mon compte',
    'access_my_account' => 'Accéder à mon compte',
    'register' => 'Créer mon compte',
    'create_an_account' => 'Créer un compte',
    'enter_login_credentials' => 'Saisissez ici vos identifiants de connexion pour accéder à votre compte.',
    'your_email' => 'Votre email',
    'your_password' => 'Votre mot de passe',
    'forgot_password' => 'Mot de passe oublié ?',
    'not_registered_yet_error_message' => <<<EOT
            Vous n'êtes pas encore inscrit à l'événement, merci de faire votre choix depuis :page_link.
        EOT,
    'login_error_message' => <<<EOT
            Votre mot de passe est erroné ou vous n'avez pas encore de compte auprès de
            l'organisateur divine id.<br>
            Si vous n'en êtes pas sûr, vous pouvez cliquer sur "Mot de passe oublié" pour
            vérifier si vous avez un compte.<br>
            Vous pouvez également créer un compte si nécessaire.
        EOT,
    'forgot_password_message' => <<<EOT
           Votre mot de passe va vous être envoyé à l'adresse indiquée ci-dessous. Pour cela, cliquez sur le bouton « Récupérer mon mot de passe ».
        EOT,
    'forgot_password_error_message' => <<<EOT
          Une erreur est survenue, veuillez réessayer, et assurez-vous que vous avez bien saisi votre adresse email.
        EOT,
    'forgot_password_no_event_error_message' => <<<EOT
          Veuillez revenir sur la <a href="#">page d'accueil</a> et sélectionner un événement d'abord.
        EOT,
    'forgot_password_success_message' => <<<EOT
           Un mail contenant vos identifiants de connexion complet vous a été envoyé.<br>
           Vous pouvez insérer votre mot de passe ci-dessous pour gérer votre inscription.
        EOT,
    'login' => 'Connexion',
    'reset_password' => 'Réinitialiser mon mot de passe',
    'email_found_notif' => <<<EOT
        Nous avons bien trouvé une correspondance dans notre système.<br>
        Si vous ne vous rappelez pas de votre mot de passe, cliquez sur le lien ci-dessous.
        EOT,
    'email_not_found_notif' => <<<EOT
        Votre adresse mail n'a pas été reconnue. Il semblerait que vous n'ayez pas de
        compte auprès de l'organisateur divine id. Nous vous proposons de créer un compte
        EOT,
    'email_not_linked_to_eligible_account' => "L'adresse email indiquée n'est pas reliée à un compte éligible"
];
