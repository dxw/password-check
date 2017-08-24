=== HIBP Check ===
Contributors: tomdxw
Tags: security, password
License: MIT
License URI: https://opensource.org/licenses/MIT

Checks password resets against haveibeenpwned.com, preventing users from using breached passwords

== Description ==

This plugin sends all changed passwords to haveibeenpwned.com's API to check whether they've been breached or not. If a password has been breached, the user is forced to choose a different one.

It's also possible to use your own API by setting the `HIBP_CHECK_URL` constant:

`define('HIBP_CHECK_URL', 'https://my-website.invalid/api/v2/pwnedpassword/%s');`

Just to be clear, if the `HIBP_CHECK_URL` constant is unset, all passwords will be sent to haveibeenpwned.com.

== Installation ==

Step 1:

If you're okay with all passwords being sent to a third-party service, you can skip this step.

However, if you want to use your own API, set the `HIBP_CHECK_URL` constant in your `wp-config.php` file. Example:

`define('HIBP_CHECK_URL', 'https://my-website.invalid/api/v2/pwnedpassword/%s');`

`%s` will be replaced with the SHA1 hash of the password.

Step 2:

Install the plugin and activate it.

== Development ==

https://github.com/dxw/hibp-check
