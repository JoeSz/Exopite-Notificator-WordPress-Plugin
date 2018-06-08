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
 * Version:           20180608
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
 * - maybe add: viber, sms, ...?
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
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EXOPITE_NOTIFICATOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EXOPITE_NOTIFICATOR_VERSION', '20180608' );
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

/*
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
        'https://update.szalai.org/?action=get_metadata&slug=' . EXOPITE_NOTIFICATOR_PLUGIN_NAME, //Metadata URL.
        __FILE__, //Full path to the main plugin file.
        EXOPITE_NOTIFICATOR_PLUGIN_NAME //Plugin slug. Usually it's the same as the name of the directory.
    );

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
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_exopite_notificator() {

	$plugin = new Exopite_Notificator( EXOPITE_NOTIFICATOR_PLUGIN_NAME );
	$plugin->run();

}
run_exopite_notificator();
