<?php
/*
Plugin Name: Password Check
Plugin URI: https://github.com/dxw/password-check
Description: Prevents the use of breached passwords by sending passwords to haveibeenpwned.com to be checked
Version: 0.1.0
Author: dxw
Author URI: https://www.dxw.com
*/

$registrar = require __DIR__.'/src/load.php';
$registrar->register();
