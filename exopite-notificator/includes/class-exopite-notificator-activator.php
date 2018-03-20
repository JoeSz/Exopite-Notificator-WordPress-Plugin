<?php

/**
 * Fired during plugin activation
 *
 * @link       https://joe.szalai.org
 * @since      1.0.0
 *
 * @package    Exopite_Notificator
 * @subpackage Exopite_Notificator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Exopite_Notificator
 * @subpackage Exopite_Notificator/includes
 * @author     Joe Szalai <joe@szalai.org>
 */
class Exopite_Notificator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate( $plugin_name ) {

        // Check if md5 exist
        $options = get_option( $plugin_name );

        if ( ! $options ) {
            $options = array();
        }

        if ( ! isset( $options['_hash'] ) ) {

            $options['_hash'] = md5( uniqid( rand(), true ) );
            update_option( $plugin_name, $options );

        }

	}

}
