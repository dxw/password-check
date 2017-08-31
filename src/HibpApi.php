<?php

namespace PasswordCheck;

class HibpApi
{
    private static $defaultUrl = 'https://haveibeenpwned.com/api/v2/pwnedpassword/%s';

    public function __construct(string $url = null)
    {
        if ($url === null) {
            $url = self::$defaultUrl;
        }

        $this->url = $url;
    }

    public function passwordIsPwned(string $password) : \Dxw\Result\Result
    {
        $url = $this->getUrl($password);
        $response = wp_remote_get($url, [
            'user-agent' => 'https://github.com/dxw/password-check',
        ]);
        if (is_wp_error($response)) {
            return \Dxw\Result\Result::err($response->get_error_message());
        }

        $code = $response['response']['code'];

        if ($code === 200) {
            return \Dxw\Result\Result::ok(true);
        } elseif ($code === 404) {
            return \Dxw\Result\Result::ok(false);
        }

        return \Dxw\Result\Result::err(sprintf("got unexpected status code: %d", $code));
    }

    private function getUrl(string $password) : string
    {
        return sprintf($this->url, sha1($password));
    }
}
