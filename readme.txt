=== Password Check ===
Contributors: tomdxw
Tags: security, password
License: MIT
License URI: https://opensource.org/licenses/MIT
Requires PHP: 7

Prevents the use of breached passwords by sending passwords to haveibeenpwned.com to be checked.

== Description ==

This plugin sends all changed password hashes to haveibeenpwned.com's API to check whether they've been breached or not. If a password has been breached, the user is unable to set it as their password. Passwords are only checked when being set or changed, not every time a user logs in.

Using this plugin will send the user's password (hashed with SHA1) to haveibeenpwned.com in order to complete the check. If you're concerned about doing that, you can set up your own API with their data in order to do the checks privately.

To do this, set the `PASSWORD_CHECK_URL` constant to the URL of your API endpoint:

`define('PASSWORD_CHECK_URL', 'https://my-website.invalid/api/v2/pwnedpassword/%s');`

If the `PASSWORD_CHECK_URL` constant is unset, all passwords will be sent to haveibeenpwned.com.

== Is this secure? ==

[Security products don't have to be perfect, they just have to be better than not using them.](https://www.troyhunt.com/password-managers-dont-have-to-be-perfect-they-just-have-to-be-better-than-not-having-one/) Our guess is that for most WordPress sites they will gain more security from avoiding known compromised passwords than they will lose from submitting the passwords to HIBP.

If submitting hashed passwords to HIBP is too much of a risk, you can use your own API (see installation instructions for how to do this).

== Installation ==

Step 1:

If you want all passwords to be sent to haveibeenpwned.com, you can skip this step.

However, if you want to use your own API, set the `PASSWORD_CHECK_URL` constant in your `wp-config.php` file. Example:

`define('PASSWORD_CHECK_URL', 'https://my-website.invalid/api/v2/pwnedpassword/%s');`

`%s` will be replaced with the SHA1 hash of the password.

Step 2:

Install the plugin and activate it.

Step 3:

WordPress has a minimum password length of 1, and passwords are hashed using an MD5-based algorithm instead of a more secure password hashing algorithm (such as bcrypt, scrypt, or PBKDF2).

We recommend addressing those issues, but we don't recommend a particular plugin. However if you think WordPress should have these (basic) features, you should register your interest on the tickets:

- https://core.trac.wordpress.org/ticket/21022
- https://core.trac.wordpress.org/ticket/35817

== Development ==

https://github.com/dxw/password-check
