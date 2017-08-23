<?php

namespace HibpCheck;

class HibpApi
{
    public function passwordIsPwned(string $password) : \Dxw\Result\Result
    {
        $response = wp_remote_get('https://haveibeenpwned.com/api/v2/pwnedpassword/'.sha1($password).'?originalPasswordIsAHash=true');
        if (is_wp_error($response)) {
            return \Dxw\Result\Result::err($response->get_error_message());
        }

        return \Dxw\Result\Result::ok($response['response']['code'] === 200);
    }
}
