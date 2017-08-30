=== Password Check ===
Contributors: tomdxw
Tags: security, password
License: MIT
License URI: https://opensource.org/licenses/MIT
Requires PHP: 7

Prevents the use of breached passwords by sending passwords to haveibeenpwned.com to be checked

== Description ==

This plugin sends all changed passwords to haveibeenpwned.com's API to check whether they've been breached or not. If a password has been breached, the user is unable to set it as their password. Passwords are only checked when being set or changed, not every time a user logs in.

It's possible to use your own API by setting the `PASSWORD_CHECK_URL` constant:

`define('PASSWORD_CHECK_URL', 'https://my-website.invalid/api/v2/pwnedpassword/%s');`

If the `PASSWORD_CHECK_URL` constant is unset, all passwords will be sent to haveibeenpwned.com.

== Installation ==

Step 1:

If you're okay with all passwords being sent to haveibeenpwned.com, you can skip this step.

However, if you want to use your own API, set the `PASSWORD_CHECK_URL` constant in your `wp-config.php` file. Example:

`define('PASSWORD_CHECK_URL', 'https://my-website.invalid/api/v2/pwnedpassword/%s');`

`%s` will be replaced with the SHA1 hash of the password.

Step 2:

Install the plugin and activate it.

== Development ==

https://github.com/dxw/password-check
