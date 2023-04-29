<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://joe.szalai.org
 * @since             1.0.0
 * @package           Exopite_Notificator
 *
 * @wordpress-plugin
 * Plugin Name:       Exopite Notificator
 * Plugin URI:        https://joe.szalai.org/exopite/exopite-notificator
 * Description:       Notify by emails or Telegram chats on selected actions. This plugin is created for security reason, more presiecly for information about potentially dangerous activities.
 * Version:           20201113
 * Author:            Joe Szalai
 * Author URI:        https://joe.szalai.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       exopite-notificator
 * Domain Path:       /languages
 */
/**
 * This plugin is created for security reason, more presiecly for information about potentially dangerous activities.
 */
/**
 * ToDos:
 * - store plugin in variable
 * - make telegram/email sending function public for other plugins to use
 * - maybe a little "API" for send? function send($where, $what, $with)
 * - maybe add:
 *   - sms!?
 *   - kik (https://github.com/pimax/kik-bot-php)
 *   - wechat (https://github.com/garbetjie/wechat-php)
 *   Not adding:
 *   - viber: must have a business/public account
 *
 * INFOS
 *   - force strong password? (if hacked and attacker try to use a week one?)
 *     https://wordpress.stackexchange.com/questions/149413/enforcing-password-complexity
 *
 * TUTORIAL:
 * https://www.siteguarding.com/en/how-to-get-telegram-bot-api-token
 * https://tutorials.botsfloor.com/creating-a-bot-using-the-telegram-bot-api-5d3caed3266d
 *
 * For a description of the Bot API, see this page: https://core.telegram.org/bots/api
 *
 * https://api.telegram.org/botXXXXXXX:YOURTOKEN/getMe
 * {"ok":true,"result":{"id":CHATID,"is_bot":true,"first_name":"BOTNAME","username":"BOTUSERNAME"}}
 *
 * https://api.telegram.org/botXXXXXXX:YOURTOKEN/getUpdates
 * https://api.telegram.org/botXXXXXXX:YOURTOKEN/sendMessage?chat_id=CHATID&text="Hello from Bot"
 */
/**
 * SMS Gateways
 * - nexmo
 * - sms77
 * - twilio
 * - https://gatewayapi.com
 * - https://www.clickatell.com
 * Chat Apps (Maybe, not all)
 * - line (https://developers.line.me/en/docs/messaging-api/overview/)
 * - skype (https://github.com/radutopala/skype-bot-php, https://dev.skype.com/bots)
 * - kik (https://botsupport.kik.com/hc/en-us/articles/225603567-How-much-does-it-cost-to-make-a-bot-)
 *       (https://github.com/pimax/kik-bot-php)
 * - Threema (https://gateway.threema.ch/en/developer/api)
 *           (https://gateway.threema.ch/en/developer/sdk-php)
 * - Facebook (https://github.com/RoySegall/facebook-messenger-send-api, https://developers.facebook.com/docs/messenger-platform/reference/send-api)
 * - wechat (http://admin.wechat.com/wiki/index.php?title=Customer_Service_Messages)
 *          (https://github.com/garbetjie/wechat-php)
 * - slack (https://api.slack.com/incoming-webhooks)
 *
 * NOT:
 * - viber (not working)
 * - google hangout etc -> cloud api
 * - tango (no official API)
 * - whatsapp (no official API)
 * - kakaotalk (closed API)
 * - BBM  (no official API yet, will be ever?)
 *
 *  WhatsApp - 55%
 *  Facebook Messenger - 31%
 *  Instagram - 26%
 *  Telegram - 16%
 *  Viber - 14%
 *  Snapchat - 13%
 *  Skype - 11%
 *  WeChat - 9%
 *  Signal - 6%
 *  Line - 5%
 *  Threema - 1%
 *  Slack - 1%
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EXOPITE_NOTIFICATOR_VERSION', '20201113' );
define( 'EXOPITE_NOTIFICATOR_PLUGIN_NAME', 'exopite-notificator' );
define( 'EXOPITE_NOTIFICATOR_PATH', plugin_dir_path( __FILE__ ) );

// $plugin_name = 'exopite-notificator';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-exopite-notificator-activator.php
 */
function activate_exopite_notificator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-exopite-notificator-activator.php';
	Exopite_Notificator_Activator::activate( EXOPITE_NOTIFICATOR_PLUGIN_NAME );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-exopite-notificator-deactivator.php
 */
function deactivate_exopite_notificator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-exopite-notificator-deactivator.php';
	Exopite_Notificator_Deactivator::deactivate( EXOPITE_NOTIFICATOR_PLUGIN_NAME );
}

register_activation_hook( __FILE__, 'activate_exopite_notificator' );
register_deactivation_hook( __FILE__, 'deactivate_exopite_notificator' );

/**
 * Update
 */
if ( is_admin() ) {

    /**
     * A custom update checker for WordPress plugins.
     *
     * Useful if you don't want to host your project
     * in the official WP repository, but would still like it to support automatic updates.
     * Despite the name, it also works with themes.
     *
     * @link http://w-shadow.com/blog/2011/06/02/automatic-updates-for-commercial-themes/
     * @link https://github.com/YahnisElsts/plugin-update-checker
     * @link https://github.com/YahnisElsts/wp-update-server
     */
    if( ! class_exists( 'Puc_v4_Factory' ) ) {

        require_once join( DIRECTORY_SEPARATOR, array( EXOPITE_NOTIFICATOR_PATH, 'vendors', 'plugin-update-checker', 'plugin-update-checker.php' ) );

    }

    $MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
        'https://update.joeszalai.org/?action=get_metadata&slug=' . EXOPITE_NOTIFICATOR_PLUGIN_NAME, //Metadata URL.
        __FILE__, //Full path to the main plugin file.
        EXOPITE_NOTIFICATOR_PLUGIN_NAME //Plugin slug. Usually it's the same as the name of the directory.
    );

    /**
     * add plugin upgrade notification
     * https://andidittrich.de/2015/05/howto-upgrade-notice-for-wordpress-plugins.html
     */
    add_action( 'in_plugin_update_message-' . EXOPITE_NOTIFICATOR_PLUGIN_NAME . DIRECTORY_SEPARATOR . EXOPITE_NOTIFICATOR_PLUGIN_NAME .'.php', 'exopite_notificator_show_upgrade_notification', 10, 2 );
    function exopite_notificator_show_upgrade_notification( $current_plugin_metadata, $new_plugin_metadata ) {

        /**
         * Check "upgrade_notice" in readme.txt.
         *
         * Eg.:
         * == Upgrade Notice ==
         * = 20180624 = <- new version
         * Notice		<- message
         *
         */
        if ( isset( $new_plugin_metadata->upgrade_notice ) && strlen( trim( $new_plugin_metadata->upgrade_notice ) ) > 0 ) {

            // Display "upgrade_notice".
            echo sprintf( '<span style="background-color:#d54e21;padding:10px;color:#f9f9f9;margin-top:10px;display:block;"><strong>%1$s: </strong>%2$s</span>', esc_attr( 'Important Upgrade Notice', 'exopite-multifilter' ), esc_html( rtrim( $new_plugin_metadata->upgrade_notice ) ) );

        }
    }

}
// End Update

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-exopite-notificator.php';

/**
 * Begins execution of the plugin.
 *
 * Allow other plugins or themes to use Exopite Notificator class methodes.
 *
 * @since    1.0.0
 */
global $exopite_notificator;
$exopite_notificator = new Exopite_Notificator( EXOPITE_NOTIFICATOR_PLUGIN_NAME );
$exopite_notificator->run();
