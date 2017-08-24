<?php

$url = defined('PASSWORD_CHECK_URL') ? PASSWORD_CHECK_URL : null;

$registrar->addInstance(new \PasswordCheck\HibpApi($url));

$registrar->addInstance(new \PasswordCheck\PasswordChange(
    $registrar->getInstance(\PasswordCheck\HibpApi::class),
    $registrar->getInstance(\Dxw\Iguana\Value\Post::class)
));
