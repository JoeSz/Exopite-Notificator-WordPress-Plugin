<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://joe.szalai.org
 * @since      1.0.0
 *
 * @package    Exopite_Notificator
 * @subpackage Exopite_Notificator/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Exopite_Notificator
 * @subpackage Exopite_Notificator/includes
 * @author     Joe Szalai <joe@szalai.org>
 */
/*
 * ToDo:
 *
 * - (admin hooks) Maybe check if log || notification type exist, then add hook
 * - Metabox for post[type]s -> update (on/off/what/where)
 * - display only available fields to selected option
 */
class Exopite_Notificator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Exopite_Notificator_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

    /**
     * Store plugin admin class to allow public access.
     *
     * @since    20180622
     * @var object      The admin class.
     */
    public $plugin_admin;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name ) {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = $plugin_name;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Exopite_Notificator_Loader. Orchestrates the hooks of the plugin.
	 * - Exopite_Notificator_i18n. Defines internationalization functionality.
	 * - Exopite_Notificator_Admin. Defines all hooks for the admin area.
	 * - Exopite_Notificator_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-exopite-notificator-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-exopite-notificator-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-exopite-notificator-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-exopite-notificator-public.php';

        /**
         * Exopite Simple Options Framework
         *
         * @link https://github.com/JoeSz/Exopite-Simple-Options-Framework
         * @author Joe Szalai
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/exopite-simple-options/exopite-simple-options-framework-class.php';

        /**
         * Notifcaster.com — sending and recieving notifcations using Telegram bot api.
         * @author Ameer Mousavi <ameer.ir>
         * forked from Notifygram by Anton Ilzheev <ilzheev@gmail.com>
         * Attention! $method always must be started with slash " / "
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendors/telegram/notifcaster.class.php';

		$this->loader = new Exopite_Notificator_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Exopite_Notificator_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Exopite_Notificator_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

        // Check if md5 exist
        $options = get_option( $this->plugin_name );

        if ( ! $options ) {
            $options = array();
        }

        if ( ! isset( $options['_hash'] ) ) {

            $options['_hash'] = md5( uniqid( rand(), true ) );
            update_option( $this->plugin_name, $options );

        }

		$this->plugin_admin = new Exopite_Notificator_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_scripts' );

        // Save/Update our plugin options
        $this->loader->add_action('init', $this->plugin_admin, 'create_menu');

        // Login success or failed
        $this->loader->add_action( 'wp_authenticate', $this->plugin_admin, 'user_login', 10, 3 );

        // Password reset
        $this->loader->add_action( 'password_reset', $this->plugin_admin, 'password_reset', 10, 2 );

        // Password change
        $this->loader->add_action( 'profile_update', $this->plugin_admin, 'profile_update', 10, 2 );

        // User register
        $this->loader->add_action( 'user_register', $this->plugin_admin, 'user_register', 10, 2 );

        /*
         * if user registration and email is on, override original functions
         */
        // Unhook the actions from wp-includes/default-filters.php
        remove_action('register_new_user', 'wp_send_new_user_notifications');
        remove_action('edit_user_created_user', 'wp_send_new_user_notifications', 10, 2);

        // Replace with our action that sends the user email only
        $this->loader->add_action('register_new_user', $this->plugin_admin, 'send_new_user_notifications');
        $this->loader->add_action('edit_user_created_user', $this->plugin_admin, 'send_new_user_notifications', 10, 2);

        // User delete
        $this->loader->add_action( 'delete_user', $this->plugin_admin, 'delete_user' );

        $this->loader->add_filter( 'registration_errors', $this->plugin_admin, 'registration_errors', 10, 3 );
        // $this->loader->add_action( 'register_post', 'prevent_register_user', 10, 3 );

        /*
         * Save Post hook
         *
         * @link https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
         * @link https://wordpress.stackexchange.com/questions/134664/what-is-correct-way-to-hook-when-update-post/134667#134667
         * @link https://stackoverflow.com/questions/44518752/which-wordpress-hook-fires-after-save-all-post-data-and-post-meta/44518930#44518930
         *
         * Add priority 100, so our hook run after meta is also saved.
         */
        $this->loader->add_action( 'save_post', $this->plugin_admin, 'post_or_page', 100, 3 );

        /*
         * Nofity users when approve a comment.
         *
         * From Plugin:
         * Plugin Name: Post Notification by Email
         * Plugin URI: http://wordpress.org/plugins/notify-users-e-mail/
         */
        $this->loader->add_action( 'wp_insert_comment', $this->plugin_admin, 'new_comment', 10, 2 );
        $this->loader->add_action( 'edit_comment', $this->plugin_admin, 'edit_comment', 10, 2 );
        $this->loader->add_action( 'transition_comment_status', $this->plugin_admin, 'comment_status', 10, 3 );

        // Contact From 7 email send
        $this->loader->add_action( 'wpcf7_before_send_mail', $this->plugin_admin, 'wpcf7_before_send_mail' );

        // Hook for other plugins and themes to send messages and access plugin functions
        $this->loader->add_action( 'shutdown', $this->plugin_admin, 'do_actions', 999, 1 );
        $this->loader->add_action( 'shutdown', $this->plugin_admin, 'send_messages_hook', 999, 1 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Exopite_Notificator_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
