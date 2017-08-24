<?php

$url = defined('HIBP_CHECK_URL') ? HIBP_CHECK_URL : null;

$registrar->addInstance(new \HibpCheck\HibpApi($url));

$registrar->addInstance(new \HibpCheck\PasswordChange(
    $registrar->getInstance(\HibpCheck\HibpApi::class),
    $registrar->getInstance(\Dxw\Iguana\Value\Post::class)
));
