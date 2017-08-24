<?php

namespace HibpCheck;

class PasswordChange implements \Dxw\Iguana\Registerable
{
    public function __construct(HibpApi $hibpApi, \Dxw\Iguana\Value\Post $__post)
    {
        $this->hibpApi = $hibpApi;
        $this->post = $__post;
    }

    public function register()
    {
        add_filter('user_profile_update_errors', [$this, 'userProfileUpdateErrors'], 20, 3);
        add_action('validate_password_reset', [$this, 'validatePasswordReset'], 20, 2);
    }

    public function userProfileUpdateErrors(\WP_Error $errors, $unused, $user)
    {
        if (!isset($user->user_pass)) {
            return;
        }

        $this->checkPass($errors, $user->user_pass);
    }

    public function validatePasswordReset(\WP_Error $errors, $user)
    {
        $this->checkPass($errors, $this->post['pass1']);
    }

    private function checkPass(\WP_Error $errors, string $password)
    {
        $result = $this->hibpApi->passwordIsPwned($password);
        if ($result->isErr()) {
            $message = $result->wrap('API error')->getErr();
            trigger_error($message, E_USER_WARNING);
            return;
        }
        $passwordIsPwned = $result->unwrap();

        if (!$passwordIsPwned) {
            return;
        }

        $errors->add('hibp-check-found', 'Password has been found in a dump. Please choose another.');
    }
}
