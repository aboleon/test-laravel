<?php


return [
    'login_to_my_account' => 'Login to my account',
    'access_my_account' => 'Access my account',
    'register' => 'Register',
    'enter_login_credentials' => 'Enter your login credentials to access your account.',
    'your_email' => 'Your email',
    'your_password' => 'Your password',
    'forgot_password' => 'Forgot password?',
    'not_registered_yet_error_message' => <<<EOT
            You are not yet registered for the event, please make your choice from :page_link.
        EOT,
    'login_error_message' => <<<EOT
            Your password is incorrect or you do not have an account with
            the organizer divine id.<br>
            If you are not sure, you can click on "Forgot password" to
            check if you have an account.<br>
            You can also create an account if necessary.
        EOT,
    'forgot_password_message' => <<<EOT
              Your password will be sent to you at the address indicated below. To do this, click on the "Retrieve my password" button.
        EOT,
    'forgot_password_error_message' => <<<EOT
            An error has occurred, please try again, and make sure you have entered your email address correctly.
        EOT,
    'forgot_password_no_event_error_message' => <<<EOT
            Please go back to the <a href="#">home page</a> and select an event first.
        EOT,
    'forgot_password_success_message' => <<<EOT
        An email containing your complete login credentials has been sent to you. <br>
        You can enter your password below to manage your registration.
        EOT,
    'login' => 'Login',
    'reset_password' => 'Reset my password',
    'email_found_notif' => <<<EOT
        We have found a match in our system.<br>
        If you don't remember your password, click on the link below.
        EOT,
    'email_not_found_notif' => <<<EOT
        Your email address was not recognized. It seems that you do not have an
        account with the organizer divine id. We suggest you create an account
        EOT,
    'email_not_linked_to_eligible_account' => 'The provided email does not belong to an eligible account'
];
