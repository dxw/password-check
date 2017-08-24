<?php
/*
Plugin Name: Password Check
Plugin URI: https://github.com/dxw/password-check
Description: Checks passwords against haveibeenpwned.com, preventing users from using breached passwords
Version: 0.1.0
Author: dxw
Author URI: https://www.dxw.com
*/

$registrar = require __DIR__.'/src/load.php';
$registrar->register();
