<?php

namespace HibpCheck;

class PasswordChange implements \Dxw\Iguana\Registerable
{
    public function __construct(HibpApi $hibpApi)
    {
        $this->hibpApi = $hibpApi;
    }

    public function register()
    {
        add_filter('user_profile_update_errors', [$this, 'userProfileUpdateErrors'], 20, 3);
        add_action('validate_password_reset', [$this, 'validatePasswordReset'], 20, 2);
    }

    public function userProfileUpdateErrors(\WP_Error $errors, $unused, \WP_User $user)
    {
        if (!isset($user->user_pass)) {
            return;
        }

        $result = $this->hibpApi->passwordIsPwned($user->user_pass);
        if ($result->isErr()) {
            //TODO: produce a warning
            return;
        }
        $passwordIsPwned = $result->unwrap();

        if (!$passwordIsPwned) {
            return;
        }

        $errors->add('name', 'message');
    }
}
