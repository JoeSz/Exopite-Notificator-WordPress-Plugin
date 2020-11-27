=== Exopite-Notificator ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://joe.szalai.org
Tags: comments, spam
Requires at least: 4.7
Tested up to: 5.5.3
Stable tag: 4.7.0
License: GPLv3 or later
Version: 20201113
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Notify by emails or Telegram chats messages on selected actions.

== Description ==

Notify by emails or Telegram chats messages on selected actions.

This plugin is created for security reason, more presiecly for information about potentially dangerous activities.

Message in all action type can be customized. Induvildual settigns in posts and pages also possible.

Available actions
- Login Success
- Login Failed
- Password Reset
- Password Changed
- E-mail Changed
- Register User Failed
- Register User Success
- User Deleted
- New Post
- Post Updated
- Post Deleted
- New Comment
- Comment Updated
- Comment Marked as Spam
- Comment Deleted
- Comment Approved
- Comment Unapproved
- Contact From 7 E-Mail sent

Available placeholders in message:
- alert-type
- datetime
- site-url
- site-name
- registration-errors
- login-type
- username
- password
- new-password
- user-ip
- user-agent
- user-email
- user-old-email
- user-roles
- user-display-name
- post-date
- post-status
- post-content
- post-title
- post-type
- post-id
- post-name
- comment-date
- comment-id
- comment-post-id
- comment-post-permalink
- comment-author
- comment-author-email
- comment-author-url
- comment-content
- comment-new_status
- comment-old_status
- cf7-[your-field-name] - all fields what user in Contact Form 7 inserted.

== Development ==

You can use this plugin to send notifications from your theme or plugins via hooks.

Template fields can be used:
- user-ip
- datetime
- site-url
- site-name
- user-agent
if user logged in
- username
- user-email
- user-display-name

exopite-notificator-send-messages filter from a class:

```php
add_filter( 'exopite-notificator-send-messages', array( $this, 'send_notification' ), 10, 1 );
public function send_notification( $messages ) {
    $messages[] = array(
        'type'                => 'telegram',
        'message'             => 'test message',
        'telegram_recipients' => 'TELEGRAM_CHAT_ID',
    );

    $messages[] = array(
        'type' => 'email',
        'message'                => 'This is the message at {{datetime}} from {{user-ip}}',
        'email_recipients'       => 'e@mail.to',
        'email_subject'          => 'Email subject',
        'email_smtp_override'    => 'yes',
        'email_disable_bloginfo' => 'yes',
    );

    return $messages;

}
```
exopite-notificator-custom action from a class:

```php
add_action( 'exopite-notificator-custom', array( $this, 'use_notificator_action_hook' ), 10, 1 );
public function use_notificator_action_hook( $notificator_object ) {
    // $notificator_object is this class with all the functions
    // var_export( $notificator_object->get_fields() );
}
```

== Installation ==

1. Upload `exopite-notificator.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 20201113 =
* Add NextCloud Talk to send messages

= 20200521 =
* Fix typo

= 20190521 =
* Update: Update Exopite Simple Options Framework

= 20181123 =
* Update Exopite Simple Options Framework.
* Add 'Exopite Client Detector' class to handle client detection.
* Various bugfixes.

= 20180622 =
* Allow other plugins or themes to use Exopite Notificator class methodes.
* Add hooks to allow other plugins or themes to send notifications

= 20180608 =
* Add SMTP override for emails. You can override all possibilities individually, but you can add only one SMTP account.

= 20180320 =
* Initial release.

== License ==

The GPL license of Sticky anything without cloning it grants you the right to use, study, share (copy), modify and (re)distribute the software, as long as these license terms are retained.

SUPPORT/UPDATES/CONTRIBUTIONS
-----------------------------

If you use my program(s), I would **greatly appreciate it if you kindly give me some suggestions/feedback**. If you solve some issue or fix some bugs or add a new feature, please share with me or mke a pull request. (But I don't have to agree with you or necessarily follow your advice.)<br/>
**Before open an issue** please read the readme (if any :) ), use google and your brain to try to solve the issue by yourself. After all, Github is for developers.<br/>
My **updates will be irregular**, because if the current stage of the program fulfills all of my needs or I do not encounter any bugs, then I have nothing to do.<br/>
**I provide no support.** I wrote these programs for myself. For fun. For free. In my free time. It does not have to work for everyone. However, that does not mean that I do not want to help.<br/>
I've always tested my codes very hard, but it's impossible to test all possible scenarios. Most of the problem could be solved by a simple google search in a matter of minutes. I do the same thing if I download and use a plugin and I run into some errors/bugs.

== Disclamer ==

NO WARRANTY OF ANY KIND! USE THIS SOFTWARES AND INFORMATIONS AT YOUR OWN RISK!
[READ DISCLAMER.TXT!](https://joe.szalai.org/disclaimer/)
License: GNU General Public License v3

[![forthebadge](http://forthebadge.com/images/badges/built-by-developers.svg)](http://forthebadge.com) [![forthebadge](http://forthebadge.com/images/badges/for-you.svg)](http://forthebadge.com)
