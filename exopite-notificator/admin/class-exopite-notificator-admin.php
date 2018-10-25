<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://joe.szalai.org
 * @since      1.0.0
 *
 * @package    Exopite_Notificator
 * @subpackage Exopite_Notificator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Exopite_Notificator
 * @subpackage Exopite_Notificator/admin
 * @author     Joe Szalai <joe@szalai.org>
 */
class Exopite_Notificator_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
    private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    private $log;
    private $hash;
    private $send_types;

    private $template_fields;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        $options = get_exopite_sof_option( $this->plugin_name );
        $this->hash = $options['_hash'];
        $this->log = ( isset( $options['log'] ) && $options['log'] == 'yes' );
        $this->send_types = array( 'email', 'telegram' );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/exopite-notificator-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/exopite-notificator-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function get_all_emails() {

        $all_users = get_users();

        $user_email_list = array();

        foreach ($all_users as $user) {
            $user_email_list[esc_html($user->user_email)] = esc_html($user->display_name);
        }

        return $user_email_list;

    }

    public function create_menu() {

        $config = array(

            'type'              => 'menu',                          // Required, menu or metabox
            'id'                => $this->plugin_name,              // Required, meta box id, unique per page, to save: get_exopite_sof_option( id )
            'menu'              => 'plugins.php',                   // Required, sub page to your options page
            'submenu'           => true,                            // Required for submenu
            'title'             => 'Exopite Notificator',            //The name of this page
            'capability'        => 'manage_options',                // The capability needed to view the page
            'plugin_basename'   =>  plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' ),
            // 'tabbed'            => false,

        );

        /*
          * To add a metabox.
          */
         $config_metabox = array(
             /*
              * METABOX
              */
             'type'              => 'metabox',                       // Required, menu or metabox
             'id'                => $this->plugin_name . '-meta',    // Required, meta box id, unique, for saving meta: id[field-id]
             'post_types'        => array( 'post', 'page' ),         // Post types to display meta box
             'context'           => 'advanced',
             'priority'          => 'default',
             'title'             => 'Exopite Notificator',                  // The name of this page
             'capability'        => 'edit_posts',                    // The capability needed to view the page
             // 'tabbed'            => false,
         );

        $fields_metabox[] = array(
            'name'   => 'general',
            'title'  => 'General',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(

                array(
                    'id'      => 'active_options',
                    'type'    => 'switcher',
                    'title'   => esc_html__( 'Activate Notifications', 'exopite-notifier' ),
                    'info'    => esc_html__( 'Turn notification in plugin option on/off on this page. (only Post Updated, Post Deleted, New Comment and Comment Updated)', 'exopite-notifier' ),
                    'default' => 'yes',
                ),

                array(
                    'id'      => 'active_post',
                    'type'    => 'switcher',
                    'title'   => esc_html__( 'Individual Post Alerts', 'exopite-notifier' ),
                    'default' => 'no',
                ),

                array(
                    'id'      => 'active_comment',
                    'type'    => 'switcher',
                    'title'   => esc_html__( 'Individual Comment Alerts', 'exopite-notifier' ),
                    'default' => 'no',
                ),

            ),
        );

        $fields_metabox[] = array(
            'name'   => 'post',
            'title'  => 'Post',
            'id'     => 'post',
            'icon'   => 'fa fa-file-text-o',
            // 'icon'   => 'dashicons-admin-page',
            'dependency' => array( 'active_post', '==', 'true' ),
            'fields' => array(

                array(
                    'id'             => 'post_alert_type',
                    'type'           => 'select',
                    'title'          => 'Alert Type',
                    'options'        => array(
                        'post-new'      => 'New Post',
                        'post-update'   => 'Post Updated',
                        'post-delete'   => 'Post Deleted',
                    ),
                    'attributes' => array(
                        'multiple' => 'multiple',
                        'style'    => 'width: 250px; height: 56px;',
                    ),
                    'class'       => 'chosen alert-action',

                ),

                array(
                    'id'             => 'post_email_recipients',
                    'type'           => 'select',
                    'title'          => 'E-Mail Recipients',
                    'options'        => 'callback',
                    'query_args'     => array(
                        'function'      => array( $this, 'get_all_emails' ),
                    ),
                    'attributes' => array(
                        'multiple' => 'multiple',
                        'style'    => 'width: 200px; height: 56px;',
                    ),
                    'class'       => 'chosen',

                ),

                array(
                    'id'      => 'post_email_recipients_additional',
                    'type'    => 'text',
                    'title'   => esc_html__( 'Additional E-Mail Recipients', 'exopite-notifier' ),
                    'after'   => '<mute>' . esc_html__( 'comma separated list', 'exopite-notifier' ) . '</mute>',
                ),

                array(
                    'id'      => 'post_email_smtp_override',
                    'type'    => 'switcher',
                    'title'   => esc_html__( 'Individual SMTP override', 'exopite-notifier' ),
                    'default' => 'no',
                    'info'   => '<mute>' . esc_html__( 'You have to set SMTP first in settings.', 'exopite-notifier' ) . '</mute>',
                ),

                array(
                    'id'      => 'post_telegram_recipients',
                    'type'    => 'text',
                    'title'   => esc_html__( 'Telegram Recipients', 'exopite-notifier' ),
                    'info'    => esc_html__( '(Chat IDs)', 'exopite-notifier' ),
                    'after'   => '<mute>' . esc_html__( 'comma separated list', 'exopite-notifier' ) . '</mute>',
                ),

                array(
                    'id'      => 'post_template',
                    'type'    => 'textarea',
                    'title'   => esc_html__( 'Notification body', 'exopite-notifier' ),
                    'class'   => 'alert-action-target-js',
                    'default' => '{{alert-type}} on {{site-name}}' . PHP_EOL . 'IP: {{user-ip}}' . PHP_EOL . 'IP: {{user-agent}}' . PHP_EOL . 'Date: {{datetime}}',
                    'after'   => '<mute>' . esc_html__( 'Available fields: ', 'exopite-notifier' ) . '<br><code class="availabe-fields">{{alert-type}}, {{datetime}}, {{site-url}}, {{site-name}}, {{user-ip}}, {{user-agent}}, {{post-date}}, {{post-status}}, {{post-content}}, {{post-title}}, {{post-type}}, {{post-id}}, {{post-name}}</code></mute>',
                ),

            ),
        );

        $fields_metabox[] = array(
            'name'   => 'comment',
            'title'  => 'Comment',
            'id'     => 'comment',
            'icon'   => 'dashicons-admin-comments',
            'dependency' => array( 'active_comment', '==', 'true' ),
            'fields' => array(

                array(
                    'id'             => 'comment_alert_type',
                    'type'           => 'select',
                    'title'          => 'Alert Type',
                    'options'        => array(
                        'comment-new'           => 'New Comment',
                        'comment-update'        => 'Comment Updated',
                    ),
                    'attributes' => array(
                        'multiple' => 'multiple',
                        'style'    => 'width: 250px; height: 56px;',
                    ),
                    'class'       => 'chosen alert-action',

                ),

                array(
                    'id'             => 'comment_email_recipients',
                    'type'           => 'select',
                    'title'          => 'E-Mail Recipients',
                    'options'        => 'callback',
                    'query_args'     => array(
                        'function'      => array( $this, 'get_all_emails' ),
                    ),
                    'attributes' => array(
                        'multiple' => 'multiple',
                        'style'    => 'width: 200px; height: 56px;',
                    ),
                    'class'       => 'chosen',

                ),

                array(
                    'id'      => 'comment_email_recipients_additional',
                    'type'    => 'text',
                    'title'   => esc_html__( 'Additional E-Mail Recipients', 'exopite-notifier' ),
                    'after'   => '<mute>' . esc_html__( 'comma separated list', 'exopite-notifier' ) . '</mute>',
                ),

                array(
                    'id'      => 'comment_email_smtp_override',
                    'type'    => 'switcher',
                    'title'   => esc_html__( 'Individual SMTP override', 'exopite-notifier' ),
                    'default' => 'no',
                    'info'   => '<mute>' . esc_html__( 'You have to set SMTP first in settings.', 'exopite-notifier' ) . '</mute>',
                ),

                array(
                    'id'      => 'comment_telegram_recipients',
                    'type'    => 'text',
                    'title'   => esc_html__( 'Telegram Recipients', 'exopite-notifier' ),
                    'info'    => esc_html__( '(Chat IDs)', 'exopite-notifier' ),
                    'after'   => '<mute>' . esc_html__( 'comma separated list', 'exopite-notifier' ) . '</mute>',
                ),

                array(
                    'id'      => 'comment_template',
                    'type'    => 'textarea',
                    'title'   => esc_html__( 'Notification body', 'exopite-notifier' ),
                    'class'   => 'alert-action-target-js',
                    'default' => '{{alert-type}} on {{site-name}}' . PHP_EOL . 'IP: {{user-ip}}' . PHP_EOL . 'IP: {{user-agent}}' . PHP_EOL . 'Date: {{datetime}}',
                    'after'   => '<mute>' . esc_html__( 'Available fields: ', 'exopite-notifier' ) . '<br><code class="availabe-fields">{{alert-type}}, {{datetime}}, {{site-url}}, {{site-name}}, {{user-ip}}, {{user-agent}}, {{comment-id}}, {{comment-post-id}}, {{comment-post-permalink}}, {{comment-author}}, {{comment-author-email}}, {{comment-author-url}}, {{comment-content}}, {{comment-date}}</code></mute>',
                ),

            ),
        );

        // Add plugin options
        $fields[] = array(
            'name'   => 'general',
            'title'  => 'General',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(

                array(
                    'type'    => 'card',
                    'class'   => 'class-name', // for all fieds
                    'content' => '<p>' . esc_html__( 'Notify by emails or Telegram chats messages on selected actions.</p><p>This plugin is created for security reason, more presiecly for information about potentially dangerous activities.', 'exopite-notifier' ) . '</p>',
                    'header' => 'Information',
                ),

                array(
                    'id'      => 'log',
                    'type'    => 'switcher',
                    'title'   => esc_html__( 'Log in file', 'exopite-notifier' ),
                    'default' => 'yes',
                    'after'   => '<small class="exopite-sof-info--small">' . esc_html__( 'Note: this will apply always for all notification type, even if you did not added. Create a separate file for each notification type. You will find the log files under:', 'exopite-notifier' ) . '<br><code>' . plugin_dir_path( __DIR__ ) . 'logs</code><br>' . esc_html__( 'Max size is 1MB, then one backup file will be created and log file will be overwritten.', 'exopite-notifier' ) . '</small>',
                ),

                array(
                    'id'      => 'metabox',
                    'type'    => 'switcher',
                    'title'   => esc_html__( 'Add metabox to posts and pages', 'exopite-notifier' ),
                    'default' => 'yes',
                ),

                array(
                    'type'    => 'backup',
                    'title'   => esc_html__( 'Backup', 'exopite-notifier' ),
                ),

            ),
        );

        $fields[] = array(
            'name'   => 'smtp',
            'title'  => esc_html__( 'SMTP Settings', 'exopite-notifier' ),
            'id'     => 'post',
            'icon'   => 'fa fa-server',
            'fields' => array(

                array(
                    'type'    => 'card',
                    'class'   => 'class-name', // for all fieds
                    'content' => esc_html__( 'You only need this, if you want to override standard WordPress/Hosting email SMTP server individually.', 'exopite-notifier' ),
                    // 'header' => 'Information',
                ),

                array(
                    'id'          => 'smtp_host',
                    'type'        => 'text',
                    'title'       => esc_html__( 'Host', 'exopite-notifier' ),
                    'attributes'    => array(
                        'placeholder' => 'mail.server.com',
                    ),
                ),

                array(
                    'id'          => 'smtp_port',
                    'type'        => 'text',
                    'title'       => esc_html__( 'Port', 'exopite-notifier' ),
                    'attributes'    => array(
                        'placeholder' => '587',
                    ),
                    'default' => '587',
                ),

                array(
                    'id'      => 'smtp_security',
                    'type'    => 'select',
                    'title'   => esc_html__( 'Security', 'exopite-notifier' ),
                    'options' => array(
                        ''          => esc_html__( 'None', 'exopite-notifier' ),
                        'tls'       => 'TLS',
                        'ssl'       => 'SSL',
                    ),
                    'default_option' => 'TLS',
                    'class'       => 'chosen',
                ),

                array(
                    'id'          => 'smtp_user',
                    'type'        => 'text',
                    'title'       => esc_html__( 'User', 'exopite-notifier' ),
                ),

                array(
                    'id'     => 'smtp_password',
                    'type'   => 'password',
                    'title'  => esc_html__( 'Password', 'exopite-notifier' ),
                ),

                array(
                    'id'      => 'smtp_html',
                    'type'    => 'switcher',
                    'title'   => 'HTML',
                    'default' => 'no',
                ),

                array(
                    'id'          => 'smtp_from_name',
                    'type'        => 'text',
                    'title'       => esc_html__( 'From Name', 'exopite-notifier' ),
                    'default' => get_bloginfo(),
                ),

                array(
                    'id'          => 'smtp_from_email',
                    'type'        => 'text',
                    'title'       => esc_html__( 'From E-Mail', 'exopite-notifier' ),
                    'default' => get_option( 'admin_email' ),
                ),

                array(
                    'id'          => 'smtp_reply_email',
                    'type'        => 'text',
                    'title'       => esc_html__( 'Reply E-Mail', 'exopite-notifier' ),
                    'default' => get_option( 'admin_email' ),
                ),
            ),
        );

        $fields[] = array(
            'name'   => 'email',
            'title'  => 'E-Mail',
            'icon'   => 'dashicons-email-alt',
            'fields' => array(

                array(
                    'type'    => 'group',
                    'id'      => 'email',
                    'title'   => 'Notifications',
                    'options' => array(
                        'repeater'          => true,
                        'accordion'         => true,
                        'button_title'      => 'Add new',
                        'accordion_title'   => 'Accordion Title',
                        'limit'             => 50,
                    ),
                    'fields'  => array(

                        array(
                            'id'      => 'email_name',
                            'type'    => 'text',
                            'title'   => esc_html__( 'Name', 'exopite-notifier' ),
                            'attributes' => array(
                                'data-title' => 'title',
                                'placeholder' => esc_html__( 'Name of the notification', 'exopite-notifier' ),
                            ),
                        ),

                        array(
                            'id'      => 'email_active',
                            'type'    => 'switcher',
                            'title'   => esc_html__( 'Active', 'exopite-notifier' ),
                            'default' => 'yes',
                        ),

                        array(
                            'id'      => 'email_smtp_override',
                            'type'    => 'switcher',
                            'title'   => esc_html__( 'Individual SMTP override', 'exopite-notifier' ),
                            'default' => 'no',
                            'info'   => '<mute>' . esc_html__( 'You have to set SMTP first in settings.', 'exopite-notifier' ) . '</mute>',
                        ),

                        array(
                            'id'             => 'email_type',
                            'type'           => 'select',
                            'title'          => 'Type',
                            'options'        => 'callback',
                            'query_args'     => array(
                                'function'      => array( $this, 'get_all_actions' ),
                            ),
                            'attributes' => array(
                                'style'    => 'width: 200px; height: 56px;',
                            ),
                            'class'       => 'chosen alert-action',

                        ),

                        array(
                            'id'             => 'email_recipients',
                            'type'           => 'select',
                            'title'          => 'Recipients',
                            'options'        => 'callback',
                            'query_args'     => array(
                                'function'      => array( $this, 'get_all_emails' ),
                            ),
                            'attributes' => array(
                                'multiple' => 'multiple',
                                'style'    => 'width: 200px; height: 56px;',
                            ),
                            'class'       => 'chosen',

                        ),

                        array(
                            'id'      => 'email_recipients_additional',
                            'type'    => 'text',
                            'title'   => esc_html__( 'Additional Recipients', 'exopite-notifier' ),
                            'after'   => '<mute>' . esc_html__( 'comma separated list', 'exopite-notifier' ) . '</mute>',
                        ),

                        array(
                            'id'      => 'email_template',
                            'type'    => 'textarea',
                            'class'   => 'alert-action-target-js',
                            'title'   => esc_html__( 'Notification body', 'exopite-notifier' ),
                            'default' => '{{alert-type}} on {{site-name}}' . PHP_EOL . 'IP: {{user-ip}}' . PHP_EOL . 'Browser: {{user-agent}}' . PHP_EOL . 'Date: {{datetime}}',
                            'after'   => '<mute>' . esc_html__( 'Available fields: ', 'exopite-notifier' ) . '<br><code class="availabe-fields">' . $this->get_fields() . '</code><br>' . esc_html( 'Note: not all field are available on all notification types. ', 'exopite-notifier' ) . '</mute>',
                        ),

                    ),

                ),

            ),
        );

        $fields[] = array(
            'name'   => 'telegram',
            'title'  => 'Telegram',
            'icon'   => 'dashicons-format-chat',
            'fields' => array(

                array(
                    'id'      => 'telegram_token',
                    'type'    => 'text',
                    'title'   => esc_html__( 'Telegram Token', 'exopite-notifier' ),
                    'after'   => '<mute>' . esc_html__( 'Note: you have to create a bot and chat first. If you do not have any,', 'exopite-notifier' ) . ' <a href="https://joe.szalai.org/exopite/exopite-notificator/create-telegram-bot/" target="_blank">' . esc_html__( 'read this instuctions.', 'exopite-notifier' ) . '</a></mute>',
                ),

                array(
                    'type'    => 'group',
                    'id'      => 'telegram',
                    'title'   => 'Notifications',
                    'options' => array(
                        'repeater'          => true,
                        'accordion'         => true,
                        'button_title'      => 'Add new',
                        'accordion_title'   => 'Accordion Title',
                        'limit'             => 50,
                    ),
                    'fields'  => array(

                        array(
                            'id'      => 'telegram_name',
                            'type'    => 'text',
                            'title'   => esc_html__( 'Name', 'exopite-notifier' ),
                            'attributes' => array(
                                'data-title' => 'title',
                                'placeholder' => esc_html__( 'Name of the notification', 'exopite-notifier' ),
                            ),
                        ),

                        array(
                            'id'      => 'telegram_active',
                            'type'    => 'switcher',
                            'title'   => esc_html__( 'Active', 'exopite-notifier' ),
                            'default' => 'yes',

                        ),

                        array(
                            'id'             => 'telegram_type',
                            'type'           => 'select',
                            'title'          => 'Type',
                            'options'        => 'callback',
                            'query_args'     => array(
                                'function'      => array( $this, 'get_all_actions' ),
                            ),
                            'attributes' => array(
                                'style'    => 'width: 200px; height: 56px;',
                            ),
                            'class'       => 'chosen alert-action',

                        ),

                        array(
                            'id'      => 'telegram_recipients',
                            'type'    => 'text',
                            'title'   => esc_html__( 'Recipients (Chat IDs)', 'exopite-notifier' ),
                            'after'   => '<mute>' . esc_html__( 'comma separated list', 'exopite-notifier' ) . '</mute>',
                        ),

                        array(
                            'id'      => 'telegram_template',
                            'type'    => 'textarea',
                            'class'   => 'alert-action-target-js',
                            'title'   => esc_html__( 'Notification body', 'exopite-notifier' ),
                            'default' => '{{alert-type}} on {{site-name}}' . PHP_EOL . 'IP: {{user-ip}}' . PHP_EOL . 'IP: {{user-agent}}' . PHP_EOL . 'Date: {{datetime}}',
                            'after'   => '<mute>' . esc_html__( 'Available fields: ', 'exopite-notifier' ) . '<br><code class="availabe-fields">' . $this->get_fields() . '</code><br>' . esc_html( 'Note: not all field are available on all notification types. ', 'exopite-notifier' ) . '</mute>',
                        ),

                    ),

                ),

            ),
        );

        $options = get_exopite_sof_option( $this->plugin_name );

        $options_panel = new Exopite_Simple_Options_Framework( $config, $fields );
        if ( ! isset( $options['metabox'] ) || $options['metabox'] == 'yes' ) $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields_metabox );

    }

    /*
     * Get Browser data from HTTP_USER_AGENT
     */
    public function get_browser() {

        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = $u_agent;
        $platform = '';
        $version= '';

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );

    }

    /*
     * Get user IP address
     */
    public function get_ip_address() {

        // check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check if multiple ips exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {
                    if ($this->validate_ip($ip))
                        return $ip;
                }
            } else {
                if ($this->validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];

        // return unreliable ip since all else failed
        return $_SERVER['REMOTE_ADDR'];

    }

    /**
     * Ensures an ip address is both a valid IP and does not fall within
     * a private network range.
     */
    public function validate_ip($ip) {

        if (strtolower($ip) === 'unknown')
            return false;

        // generate ipv4 network address
        $ip = ip2long($ip);

        // if the ip is set and not equivalent to 255.255.255.255
        if ($ip !== false && $ip !== -1) {
            // make sure to get unsigned long representation of ip
            // due to discrepancies between 32 and 64 bit OSes and
            // signed numbers (ints default to signed in PHP)
            $ip = sprintf('%u', $ip);
            // do private network range checking
            if ($ip >= 0 && $ip <= 50331647) return false;
            if ($ip >= 167772160 && $ip <= 184549375) return false;
            if ($ip >= 2130706432 && $ip <= 2147483647) return false;
            if ($ip >= 2851995648 && $ip <= 2852061183) return false;
            if ($ip >= 2886729728 && $ip <= 2887778303) return false;
            if ($ip >= 3221225984 && $ip <= 3221226239) return false;
            if ($ip >= 3232235520 && $ip <= 3232301055) return false;
            if ($ip >= 4294967040) return false;
        }

        return true;

    }

    public function get_login_type() {

        if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {

            return 'XMLRPC';

        } elseif ( defined('JSON_REQUEST') && JSON_REQUEST ) {

            return 'JSON';

        } elseif ( defined('REST_REQUEST') && REST_REQUEST ) {

            return 'REST-API';

        } else {

            $referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
            if ( strstr( $referrer, 'wp-login' ) || strstr( $referrer, 'wp-admin' ) ) {

                return 'WP-LOGIN';

            } else {

                return 'UNKNOWN';

            }

        }

    }

    public function write_log( $type, $log_line ) {

        $fn = EXOPITE_NOTIFICATOR_PLUGIN_DIR . 'logs/' . $type . '-' . $this->hash . '.log';
        if ( ! file_exists( $fn ) ) $file_size = 0;
        $file_size = filesize( $fn );

        // If log file is bigger then 1MB, rename it to backup.log and start new log,
        // in this case we have max 2MB log file per type.
        if ( $file_size > 1000000 ) {
            rename( $fn, EXOPITE_NOTIFICATOR_PLUGIN_DIR . 'logs/' . $type . '-' . $this->hash . '.backup.log' );
        }
        $log_in_file = file_put_contents( $fn, date('Y-m-d H:i:s') . ' - ' . $log_line . PHP_EOL, FILE_APPEND );

    }

    /**
     * https://core.telegram.org/bots
     * https://tutorials.botsfloor.com/creating-a-bot-using-the-telegram-bot-api-5d3caed3266d
     * https://www.sohamkamani.com/blog/2016/09/21/making-a-telegram-bot/
     * https://stackoverflow.com/questions/32423837/telegram-bot-how-to-get-a-group-chat-id/32572159#32572159
     * https://github.com/php-telegram-bot/core
     * https://medium.com/@xabaras/sending-a-message-to-a-telegram-channel-the-easy-way-eb0a0b32968
     * https://tutorials.botsfloor.com/creating-a-bot-using-the-telegram-bot-api-5d3caed3266d
     * https://www.forsomedefinition.com/automation/creating-telegram-bot-notifications/
     * https://www.sohamkamani.com/blog/2016/09/21/making-a-telegram-bot/
     */
    public function send_message_telegram( $item, $message ) {

        $options = get_exopite_sof_option( $this->plugin_name );

        // If no recipient definied, then try default one
        if ( empty( $item['telegram_recipients'] ) ) $item['telegram_recipients'] = $options['telegram_default_channel'];
        $telegram_channels_options = apply_filters( 'telegram_recipients', $item['telegram_recipients'], $item, $message );
        if ( empty( $telegram_channels_options ) ) return;
        $telegram_channels = array_filter( explode( ',', preg_replace( '/\s+/', '', $telegram_channels_options ) ) );

        $message = apply_filters( 'exopite-notificator-telegram-before-body', '', $item, $message ) .
                   apply_filters( 'exopite-notificator-telegram-body', $message, $item ) .
                   apply_filters( 'exopite-notificator-telegram-after-body', '', $item, $message );

        $message = html_entity_decode( $message );

        // $this->write_log( '_telegram_send_messgae', 'item: ' . var_export( $item, true ) );

        do_action( 'exopite-notificator-telegram-before-send', $item, $message, $options );

        if ( isset( $options['telegram_token'] ) && ! empty( $options['telegram_token'] ) ) {

            foreach ( $telegram_channels as $telegram_channel ) {

                $notifcaster = new Notifcaster_Class();
                $notifcaster->_telegram( apply_filters( 'exopite-notificator-telegram-token', $options['telegram_token'], $item, $message ) );

                $sentResult = $notifcaster->channel_text( $telegram_channel, $message );

            }

        }

        do_action( 'exopite-notificator-telegram-after-send', $item, $message, $options );

    }

    public function send_mail( $emails, $subject, $message ) {

        if ( ! class_exists( 'PHPMailer' ) ) {
            include_once( ABSPATH . WPINC . '/class-phpmailer.php' );
        }

        $options = get_exopite_sof_option( $this->plugin_name );

        $mail = new PHPMailer();
        if ( $options['smtp_html'] == 'yes' ) {
            $mail->IsHTML( true );
        } else {
            $mail->ContentType = 'text/plain';
            $mail->IsHTML( false );
        }

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $options['smtp_host'];  // Specify main and backup server
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $options['smtp_user'];                            // SMTP username
        $mail->Password = $options['smtp_password'];                           // SMTP password
        if ( ! empty( $options['smtp_security'] ) ) $mail->SMTPSecure = $options['smtp_security'];                            // Enable encryption, 'ssl' also accepted
        $mail->Port = $options['smtp_port'];
        $mail->SetFrom( $options['smtp_from_email'], apply_filters( 'exopite-notificator-sender-name', $options['smtp_from_name'] ) );
        $mail->addReplyTo( $options['smtp_reply_email'], apply_filters( 'exopite-notificator-sender-name', $options['smtp_from_name'] ) );
        $mail->CharSet = 'UTF-8';

        // Check comma, if yes, explode
        if( ! is_array( $emails ) && strpos( $emails, ',' ) !== false ) {
            $emails = explode( ',', $emails );
        }

        $emails = apply_filters( 'exopite-notificator-subject', $emails );

        if ( is_array( $emails ) ) {
            foreach ( $emails as $email ) {
                $mail->AddAddress( $email );
            }
        } else {
            $mail->AddAddress( $emails );
        }


        $mail->Subject = apply_filters( 'exopite-notificator-subject', $subject );
        $mail->Body = apply_filters( 'exopite-notificator-message', $message );
        $info = $mail->Send();

        // if ( $this->log ) {
        //     $this->write_log( 'send_message_email', 'emails: ' . var_export( $emails, true ) );
        //     $this->write_log( 'send_message_email', 'info: ' . var_export( $info, true ) );
        //     $this->write_log( 'send_message_email', 'email: ' . var_export( $mail, true ) );
        //     $this->write_log( 'send_message_email', 'options: ' . var_export( $options, true ) );
        //     $this->write_log( 'send_message_email', '------------------------------------------------------------------' . PHP_EOL . PHP_EOL );
        // }

        return $info;

    }

    public function send_message_email( $item, $message ) {

        // write the email content
        $header = "MIME-Version: 1.0\n";
        $header .= "Content-Type: text/html; charset=utf-8\n";
        $header .= esc_html__( 'From', 'exopite-notifier' ) . ": WordPress " . get_bloginfo( 'name' ) . " <" . get_bloginfo( 'admin_email' ) . ">\n";

        $actions = $this->get_all_actions();

        $to = array();
        $email_recipients = array( 'email_recipients', 'post_email_recipients', 'comment_email_recipients' );
        foreach ( $email_recipients as $email_recipient ) {

            if ( ! empty( $item[$email_recipient] ) && is_array( $item[$email_recipient] ) ) {
                $to = $item[$email_recipient];
            } elseif( ! empty( $item[$email_recipient] ) ) {
                // If not an array, then it is a comma separated list on one email address
                $to = explode( ',', $item[$email_recipient] );
            }
        }

        $email_recipients_additional = array( 'email_recipients_additional', 'post_email_recipients_additional', 'comment_email_recipients_additional' );
        $to_extra = array();
        foreach ( $email_recipients_additional as $email_recipient_additional ) {
            $to_additional = apply_filters( $email_recipient_additional, $item[$email_recipient_additional], $item, $message );

            if ( isset( $to_additional ) && ! empty( $to_additional ) ) {
                $to_extra = explode( ',', preg_replace( '/\s+/', '', $to_additional ) );
            }
        }

        $to = array_filter( array_merge( $to, $to_extra ) );

        if ( empty( $to ) ) {

            if ( $this->log ) $this->write_log( 'email-failed', 'There is/are no email/s to send.' );

            return;
        }

        // Generate subject
        $email_subject = array();
        $email_subject[] = ( empty( $item['email_disable_bloginfo'] ) || $item['email_disable_bloginfo'] != 'yes' ) ? get_bloginfo( 'name' ) : '';
        $email_subject[] = ( empty( $actions[$item['email_type']] ) ) ? $item['email_type'] : $actions[$item['email_type']];
        $subject = "=?utf-8?B?" . base64_encode( apply_filters( 'exopite-notificator-email-subject', implode( ' | ', array_filter( $email_subject ) ), $item ) ) . "?=";

        // Hook to filter message
        $body = apply_filters( 'exopite-notificator-email-before-body', '', $item, $message );
        $body .= apply_filters( 'exopite-notificator-email-body', $message, $item );
        $body .= apply_filters( 'exopite-notificator-email-after-body', '', $item, $message );

        do_action( 'exopite-notificator-email-before-send', $item, $message, $to, $subject, $body, $header );

        if ( $item['email_smtp_override'] == 'yes' || $item['post_email_smtp_override'] == 'yes' || $item['comment_email_smtp_override'] == 'yes' ) {

            $ret =  $this->send_mail( $to, $subject, $body );

        } else {

            $ret =  wp_mail( $to, $subject, $body, $header );

        }

        if ( ! $ret ) {

            if ( $this->log ) $this->write_log( 'email-failed', 'Sending email failed to ' . var_export( $to, true ) );

        } else {

            if ( $this->log ) $this->write_log( 'email-success', 'Email sent to ' . var_export( $to, true ) );

        }

        do_action( 'exopite-notificator-email-after-send', $item, $message, $to, $subject, $body, $header );

        return $ret;

    }

    /**
     * How to use (eg:)
     *
     * add_filter( 'exopite-notificator-send-messages', array( $this, 'send_notification' ), 10, 1 );
     * public function send_notification( $messages ) {
     *     $messages[] = array(
     *         'type'                => 'telegram',
     *         'message'             => 'test message',
     *         'telegram_recipients' => 'TELEGRAM_CHAT_ID',
     *         'alert-type'          => 'Type of the alert',
     *     );
     *
     *     $messages[] = array(
     *         'type' => 'email',
     *         'message'                => 'This is the message at {{datetime}} from {{user-ip}}',
     *         'email_recipients'       => 'e@mail.to',
     *         'email_subject'          => 'Email subject',
     *         'email_smtp_override'    => 'yes',
     *         'email_disable_bloginfo' => 'yes',
     *     );
     *
     *     return $messages;
     *
     * }
     */
    public function send_messages_hook() {

        $messages = apply_filters( 'exopite-notificator-send-messages', array() );

        if ( ! empty( $messages ) && is_array( $messages ) ) {
            foreach ( $messages as $message ) {
                $item = array();
                // comma separats list
                $item[$message['type'] . '_recipients'] = $message[$message['type'] . '_recipients'];
                $item['email_type'] = ( isset( $message['email_subject'] ) ) ? $message['email_subject'] : '';
                $item['email_smtp_override'] = ( isset( $message['email_subject'] ) ) ? $message['email_smtp_override'] : '';
                $item['email_disable_bloginfo'] = ( isset( $message['email_subject'] ) ) ? $message['email_disable_bloginfo'] : '';
                // Get default fields
                $template_fields = $this->get_template_fields();
                $template_fields['alert-type'] =  ( isset( $message['alert-type'] ) ) ? $message['alert-type'] : '';
                call_user_func_array( array( $this, 'send_message_' . $message['type'] ), array( $item, $this->generate_template( $template_fields, $message['message'] ) ) );
            }
        }

    }

    /**
     * How to use (eg:)
     *
     * add_action( 'exopite-notificator-custom', array( $this, 'use_notificator_action_hook' ), 10, 1 );
     * public function use_notificator_action_hook( $notificator_object ) {
     *     // $notificator_object is this class with all the functions
     *     var_export( $notificator_object->get_fields() );
     * }
     *
     */
    public function do_actions() {

        do_action( 'exopite-notificator-custom', $this );

    }

    public function get_fields() {

        $fields = array(
            'alert-type',
            'datetime',
            'site-url',
            'site-name',
            'registration-errors',
            'login-type',
            'username',
            'password',
            'new-password',
            'user-ip',
            'user-agent',
            'user-email',
            'user-old-email',
            'user-roles',
            'user-display-name',
            'post-date',
            'post-status',
            'post-content',
            'post-title',
            'post-type',
            'post-id',
            'post-name',
            'comment-date',
            'comment-id',
            'comment-post-id',
            'comment-post-permalink',
            'comment-author',
            'comment-author-email',
            'comment-author-url',
            'comment-content',
            'comment-new_status',
            'comment-old_status',
            'cf7-[your-field-name]'
        );

        return '{{' . implode( '}}, {{', $fields ) . '}}';

    }

    public function get_all_actions() {
        return  array(
            'login-success'         => 'Login Success',
            'login-failed'          => 'Login Failed',
            'password-reset'        => 'Password Reset',
            'password-changed'      => 'Password Changed',
            'email-changed'         => 'E-mail Changed',
            'user-register-failed'  => 'Register User Failed',
            'user-register-success' => 'Register User Success',
            'user-delete'           => 'User Deleted',
            'post-new'              => 'New Post',
            'post-update'           => 'Post Updated',
            'post-delete'           => 'Post Deleted',
            'comment-new'           => 'New Comment',
            'comment-update'        => 'Comment Updated',
            'comment-spam'          => 'Comment Marked as Spam',
            'comment-delete'        => 'Comment Deleted',
            'comment-approved'      => 'Comment Approved',
            'comment-unapproved'    => 'Comment Unapproved',
            'cf7-email-sent'        => 'Contact From 7 E-Mail sent',
        );
    }

    // Replace template tags to variable value
    public function generate_template( $template_fields, $template ) {

        $actions = $this->get_all_actions();

        if ( ( isset( $actions[$template_fields['alert-type']] ) ) ) {
            $template_fields['alert-type'] = $actions[$template_fields['alert-type']];
        }

        foreach ( $template_fields as $name => $value ) {
            $template = str_replace(
                '{{' . $name . '}}',
                $value,
                $template
            );
        }

        $template = preg_replace(
            '/{{([^]]*)}}/',
            "",
            $template
        );

        return $template;

    }

    // Fields for all types
    public function get_template_fields() {

        $user_browser = $this->get_browser();

        $template_fields = array(
            'user-ip'       => $this->get_ip_address(),
            'datetime'      => date('Y-m-d H:i:s'),
            'site-url'      => get_site_url(),
            'site-name'     => get_bloginfo( 'name' ),
            'user-agent'    => $user_browser['name'] . " | " . $user_browser['version'] . " | " . $user_browser['platform'],
        );

        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            $template_fields['username'] = $current_user->user_login;
            $template_fields['user-email'] = $current_user->user_email;
            $template_fields['user-display-name'] = $current_user->display_name;
        }

        return apply_filters( 'exopite-notificator-get-default-template-fields', $template_fields );

    }

    // Loop plugin options group fields (later can be add more norification platform like viber, etc...)
    public function loop_options( $callback, $args = array() ) {

        $options = get_exopite_sof_option( $this->plugin_name );

        // Loop all options
        foreach ( $options as $key => $option ) {

            // Only arrays (Group fields)
            if ( is_array( $option ) ) {

                // All options (alert types) in there
                foreach ( $option as $item ) {

                    $args['key'] = $key;
                    $args['item'] = $item;

                    // Check if alert type is activated
                    if ( $item[$key . '_active'] != 'yes' ) continue;

                    // Run callback.
                    call_user_func_array( $callback, $args );

                }

            }

        }

    }

    // Callback function
    public function do_action( $template_fields, $key, $item ) {

        // Add alert type to template fields
        $template_fields['alert-type'] = $item[$key . '_type'];

        // if ( $this->log ) $this->write_log( '_'.$key.'_group__' . $item[$key . '_type'], 'item: ' . var_export( $item, true ) );
        // if ( $this->log ) $this->write_log( '_'.$key.'_group__' . $item[$key . '_type'], 'template_fields: ' . var_export( $template_fields, true ) );
        // if ( $this->log ) $this->write_log( '_'.$key.'_group__' . $item[$key . '_type'], 'template: ' . $this->generate_template( $template_fields, $item[$key . '_template'] ) );

        // Run callback functions (send_message_email; send_message_telegram; maybe more later)
        call_user_func_array( array( $this, 'send_message_' . $key ), array( $item, $this->generate_template( $template_fields, $item[$key . '_template'] ) ) );

    }

    // Generate log based on template fields as: key: value - key2: value2, ...
    public function generate_log( $type, $template_fields ) {

        $logline = array();

        $ignore = array( 'post-content', 'comment-content' );

        foreach ( $template_fields as $key => $value ) {

            if ( in_array( $key, $ignore ) ) continue;

            $logline[] = $key . ': ' . $value;

        }

        $this->write_log( $type, implode( ' - ', $logline ) );

    }

    /**
     * Fires before the user's password is reset, but not on reset request
     */
    public function password_reset( $user_info, $new_pass ) {

        // Get default fields
        $template_fields = $this->get_template_fields();

        // Add fields based on hook
        $template_fields['username'] = $user_info->user_login;
        $template_fields['user-roles'] = implode( ',', $user_info->roles );
        $template_fields['user-display-name'] = implode( ',', $user_info->display_name );
        $template_fields['user-email'] = implode( ',', $user_info->user_email );
        $template_fields['new-password'] = $new_pass;

        // Build args to pass for anonym (loop with callbacks) function.
        $args = array(
            $template_fields
        );

        // Create log if requested.
        if ( $this->log ) $this->generate_log( 'password-reset', $template_fields );

        /**
         * - loop plugin options
         * - run callback: do_action
         * - run callback: send_message_[type]
         */

        $this->loop_options( function( $template_fields, $key, $item ) {

            // Run only if password-reset is found.
            if ( $item[$key . '_type'] == 'password-reset' ) {

                $this->do_action( $template_fields, $key, $item );

            }

        }, $args );

    }

    /**
     * Login success and failed callback
     */
    public function user_login( $username, $password ) {

        if ( ! empty( $username ) && ! empty( $password ) ) {

            $options = get_exopite_sof_option( $this->plugin_name );

            // Check username type, string or email
            if ( filter_var( $username, FILTER_VALIDATE_EMAIL ) ) {

                $authenticated = wp_authenticate_email_password( NULL, $username, $password );

            } else {

                $authenticated = wp_authenticate_username_password( NULL, $username, $password );

            }

            $template_fields = $this->get_template_fields();

            $template_fields['password'] = $password;
            $template_fields['username'] = $username;
            $template_fields['login-type'] = $this->get_login_type();

            if ( empty( $authenticated->errors ) ) {

                if ( is_array( $authenticated->roles ) ) {
                    $template_fields['roles'] = implode( ',', $authenticated->roles );
                }

            }

            if ( empty( $authenticated->errors ) ) {

                if ( $this->log ) $this->generate_log( 'login-success', $template_fields );

            } elseif ( ! empty( $authenticated->errors ) ) {

                if ( $this->log ) $this->generate_log( 'login-failed', $template_fields );

            }

            $args = array(
                $template_fields,
                $authenticated
            );

            $this->loop_options( function( $template_fields, $authenticated, $key, $item ) {

                if ( empty( $authenticated->errors ) && $item[$key . '_type'] == 'login-success' ) {

                    $this->do_action( $template_fields, $key, $item );

                } elseif ( ! empty( $authenticated->errors ) && $item[$key . '_type'] == 'login-failed' ) {

                    $this->do_action( $template_fields, $key, $item );

                }

            }, $args );

        }

    }

    public function profile_update( $user_id, $old_user_data ) {

        $user_info = get_userdata( $user_id );

        if ( ( ( ! isset( $_POST['pass1'] ) || '' == $_POST['pass1'] ) || (  ! $_POST['pass1'] === $_POST['pass2']  ) ) && $old_user_data->user_email == $user_info->user_email ) {

            // Run only if relevant fields changed.
            return;
        }

        $template_fields = $this->get_template_fields();

        $template_fields['username'] = $user_info->user_login;
        $template_fields['user-roles'] = implode( ',', $user_info->roles );
        $template_fields['user-display-name'] = $user_info->display_name;
        $template_fields['user-email'] = $user_info->user_email;

        if ( ! empty( $post_pass1 ) ) {

            $template_fields['new-password'] = $post_pass1;

            if ( $this->log ) $this->generate_log( 'password-changed', $template_fields );

        }

        if ( $old_user_data->user_email != $user_info->user_email ) {

            $template_fields['user-old-email'] = $old_user_data->user_email;

            if ( $this->log ) $this->generate_log( 'email-changed', $template_fields );
        }

        $args = array(
            $template_fields,
            $user_info,
            $old_user_data,
            $_POST['pass1']
        );

        $this->loop_options( function( $template_fields, $user_info, $old_user_data, $post_pass1, $key, $item ) {

            if ( $item[$key . '_type'] == 'password-changed' && ! empty( $post_pass1 ) ) {

                $this->do_action( $template_fields, $key, $item );

            }

            if ( $item[$key . '_type'] == 'email-changed' && $old_user_data->user_email != $user_info->user_email ) {

                $this->do_action( $template_fields, $key, $item );
            }

        }, $args );

    }

    /**
     * Plugin Name: Disable User Registration Notification Emails
     * Description: Turns off the notification sent to the admin email when a new user account is registered. Works with WP >= 4.6.0.
     * Version: 1.0.0
     * Author: Potent Plugins
     * Author URI: http://potentplugins.com/?utm_source=disable-user-registration-notification-emails&utm_medium=link&utm_campaign=wp-plugin-author-uri
     * License: GNU General Public License version 2 or later
     * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
     */
    public function send_new_user_notifications( $user_id, $to = 'both' ) {

        if ( empty( $to ) || $to == 'admin' ) {

            // Admin only, so we don't do anything
            return;

        }

        // For 'both' or 'user', we notify only the user
        wp_send_new_user_notifications( $user_id, 'user' );

    }

    public function registration_errors( $errors, $sanitized_user_login, $user_email ) {

        $error_message = array();

        foreach ( $errors->errors as $key => $value ) {
            $error_message[] = $key;
        }

        if ( empty( $error_message ) ) return $errors;

        $template_fields = $this->get_template_fields();

        $template_fields['registration-errors'] = implode( ',', $error_message );
        $template_fields['username'] = $sanitized_user_login;
        $template_fields['user-email'] = $user_email;

        if ( $this->log ) $this->generate_log( 'user-register-failed', $template_fields );

        $args = array(
            $template_fields,
        );

        $this->loop_options( function( $template_fields, $key, $item ) {

            if ( $item[$key . '_type'] == 'user-register-failed' ) {

                $this->do_action( $template_fields, $key, $item );

            }

        }, $args );

        return $errors;

    }

    public function delete_user( $user_id ) {

        global $wpdb;

        $user_info = get_userdata( $user_id );

        $template_fields = $this->get_template_fields();

        $template_fields['username'] = $user_info->user_login;
        $template_fields['user-roles'] = implode( ',', $user_info->roles );
        $template_fields['user-display-name'] = $user_info->display_name;
        $template_fields['user-email'] = $user_info->user_email;

        if ( $this->log ) $this->generate_log( 'user-delete', $template_fields );

        $args = array(
            $template_fields,
        );

        $this->loop_options( function( $template_fields, $key, $item ) {

            if ( $item[$key . '_type'] == 'user-delete' ) {

                $this->do_action( $template_fields, $key, $item );

            }

        }, $args );

    }

    public function user_register( $user_id ) {

        $user_info = get_userdata( $user_id );

        $template_fields = $this->get_template_fields();

        $template_fields['username'] = $user_info->user_login;
        $template_fields['user-roles'] = implode( ',', $user_info->roles );
        $template_fields['user-display-name'] = $user_info->display_name;
        $template_fields['user-email'] = $user_info->user_email;

        if ( $this->log ) $this->generate_log( 'user-register-success', $template_fields );

        $args = array(
            $template_fields,
        );

        $this->loop_options( function( $template_fields, $key, $item ) {

            if ( $item[$key . '_type'] == 'user-register-success' ) {

                $this->do_action( $template_fields, $key, $item );

            }

        }, $args );

    }

    /*
     * https://wordpress.stackexchange.com/questions/162642/contact-form-7-pre-email-processing/162645#162645
     * http://securitydawg.com/changing-contact-form-7-with-the-wpcf7_before_send_mail-hook/
     * http://www.dunnies-it.com/wordpress/fxing-wpcf7_before_send_mail-hooks-contact-form-7-plugin.php
     */
    public function wpcf7_before_send_mail( $contact_form ) {

        $template_fields = $this->get_template_fields();

        $submission = WPCF7_Submission::get_instance();

        if( $submission ) {

            $posted_data = $submission->get_posted_data();

            foreach ($posted_data as $keyval => $posted) {

                if ( substr( $keyval, 0, 1 ) !== '_') {

                    // Add all Contact Form 7 placeholders to template fields
                    $template_fields['cf7-' . $keyval] = $posted;

                }

            }

        }

        $args = array(
            $template_fields,
            $contact_form
        );

        if ( $this->log ) $this->generate_log( 'cf7-email-sent', $template_fields );

        $this->loop_options( function( $template_fields, $contact_form, $key, $item ) {

            if ( $item[$key . '_type'] == 'cf7-email-sent' ) {

                $this->do_action( $template_fields, $key, $item );

            }

        }, $args );

    }

    /*
     * Handle new comment and update comment actions for post/page meta
     */
    public function do_meta_comment_actions( $post_meta, $template_fields, $type ) {

        if ( isset( $post_meta[0]['active_comment'] ) && $post_meta[0]['active_comment'] == 'yes' ) {

            $item_meta = array();
            $item_meta['telegram_recipients'] = $post_meta[0]['comment_telegram_recipients'];
            $item_meta['email_recipients'] = $post_meta[0]['comment_email_recipients'];
            $item_meta['email_recipients_additional'] = $post_meta[0]['comment_email_recipients_additional'];
            $item_meta['email_smtp_override'] = $post_meta[0]['comment_email_smtp_override'];

            $item_meta['email_type'] = $type;
            $template_fields['alert-type'] = $type;

            // Type is email and/or telegram
            foreach ( $this->send_types as $send_type ) {

                if ( in_array( $type, $post_meta[0]['comment_alert_type'] ) ){

                    call_user_func_array( array( $this, 'send_message_' . $send_type ), array( $item_meta, $this->generate_template( $template_fields, $post_meta[0]['comment_template'] ) ) );

                }

            }

        }

    }

    /**
     * @param int      $id Comment ID.
     * @param stdClass $comment Comment data.
     *
     * @return void
     */
    public function new_comment( $id, $comment ) {

        $template_fields = $this->get_template_fields();

        $template_fields['comment-id'] = $id;
        $template_fields['comment-post-id'] = $comment->comment_post_ID;
        $template_fields['comment-post-permalink'] = get_permalink( $comment->comment_post_ID );
        $template_fields['comment-author'] = $comment->comment_author;
        $template_fields['comment-author-email'] = $comment->comment_author_email;
        $template_fields['comment-author-url'] = $comment->comment_author_url;
        $template_fields['comment-content'] = $comment->comment_content;
        $template_fields['comment-date'] = $comment->comment_date;

        if ( $this->log ) $this->generate_log( 'comment-new', $template_fields );

        $args = array(
            $template_fields,
            $comment
        );

        $active_options = true;

        $options = get_exopite_sof_option( $this->plugin_name );

        if ( $this->log ) $this->write_log( 'post-page-saved_metabox', var_export( $options['metabox'], true ) );

        // If metabox in plugin options is activated
        if ( ! isset( $options['metabox'] ) || $options['metabox'] == 'yes' ) {

            // Get post/page meta
            $post_meta = get_post_meta( $comment->comment_post_ID, $this->plugin_name . '-meta' );

            $item['email_smtp_override'] = $post_meta[0]['comment_email_smtp_override'];
            if ( $this->log ) $this->write_log( 'comment-new', 'postmeta: ' . var_export( $post_meta, true ) );

            // Check if plugin option override is activated.
            // User can turn off alert in meta for new comment, comment update, post update and post delete.
            if ( isset( $post_meta[0]['active_options'] ) && $post_meta[0]['active_options'] == 'no' ) {

                $active_options = false;

            }

            // Do meta actions
            $this->do_meta_comment_actions( $post_meta, $template_fields, 'comment-new' );

        }

        // Only if post/page meta "plugin option override" is not activated.
        if ( $active_options ) {

            $this->loop_options( function( $template_fields, $comment, $key, $item ) {

                if ( $item[$key . '_type'] == 'comment-new' ) {

                    $this->do_action( $template_fields, $key, $item );

                }

            }, $args );

        }

    }

    /**
     * @param string   $new_status New status of comment.
     * @param string   $old_status Old status of comment.
     * @param stdClass $comment Comment data.
     *
     * @return void
     */
    public function edit_comment( $id, $comment ) {

        $template_fields = $this->get_template_fields();

        $template_fields['comment-id'] = $id;
        $template_fields['comment-post-id'] = $comment['comment_post_ID'];
        $template_fields['comment-post-permalink'] = get_permalink( $comment['comment_post_ID'] );
        $template_fields['comment-author'] = $comment['comment_author'];
        $template_fields['comment-author-email'] = $comment['comment_author_email'];
        $template_fields['comment-author-url'] = $comment['comment_author_url'];
        $template_fields['comment-content'] = $comment['comment_content'];
        $template_fields['comment-date'] = $comment['comment_date'];

        if ( $this->log ) $this->generate_log( 'comment-update', $template_fields );

        $args = array(
            $template_fields,
            $comment
        );

        $active_options = true;

        $options = get_exopite_sof_option( $this->plugin_name );

        if ( ! isset( $options['metabox'] ) || $options['metabox'] == 'yes' ) {

            // POST META
            $post_meta = get_post_meta( $comment['comment_post_ID'], $this->plugin_name . '-meta' );
            $item['email_smtp_override'] = $post_meta[0]['comment_email_smtp_override'];

            if ( isset( $post_meta[0]['active_options'] ) && $post_meta[0]['active_options'] == 'no' ) {

                $active_options = false;

            }

            $this->do_meta_comment_actions( $post_meta, $template_fields, 'comment-update' );

        }

        if ( $active_options ) {

            $this->loop_options( function( $template_fields, $comment, $key, $item ) {

                if ( $item[$key . '_type'] == 'comment-update' ) {

                    $this->do_action( $template_fields, $key, $item );

                }

            }, $args );

        }

    }

    /**
     * @param string   $new_status New status of comment.
     * @param string   $old_status Old status of comment.
     * @param stdClass $comment Comment data.
     *
     * @return void
     */
    public function comment_status( $new_status, $old_status, $comment ) {

        $template_fields = $this->get_template_fields();

        $template_fields['comment-id'] = $comment->comment_ID;
        $template_fields['comment-post-id'] = $comment->comment_post_ID;
        $template_fields['comment-post-permalink'] = get_permalink( $comment->comment_post_ID );
        $template_fields['comment-author'] = $comment->comment_author;
        $template_fields['comment-author-email'] = $comment->comment_author_email;
        $template_fields['comment-author-url'] = $comment->comment_author_url;
        $template_fields['comment-content'] = $comment->comment_content;
        $template_fields['comment-date'] = $comment->comment_date;
        $template_fields['comment-new_status'] = $new_status;
        $template_fields['comment-old_status'] = $old_status;

        if ( $this->log ) $this->generate_log( 'comment-status-changed', $template_fields );

        $args = array(
            $template_fields,
            $comment,
            $new_status,
            $old_status
        );

        $this->loop_options( function( $template_fields, $comment, $new_status, $old_status, $key, $item ) {

            if ( $old_status != $new_status ) {

                if ( ( $new_status == 'spam' && $item[$key . '_type'] == 'comment-spam' ) ||
                     ( $new_status == 'trash' && $item[$key . '_type'] == 'comment-delete' ) ||
                     ( $new_status == 'approved' && $item[$key . '_type'] == 'comment-approved' ) ||
                     ( $new_status == 'unapproved' && $item[$key . '_type'] == 'comment-unapproved' )
                   ){

                    $this->do_action( $template_fields, $key, $item );

                }

            }

        }, $args );

    }

    /**
     * Send email on save post and/or page
     *
     * @param int   $post_ID    The post ID.
     * @param post  $post       The post object.
     * @param bool  $update     Whether this is an existing post being updated or not.
     *
     * @return void
     */
    public function post_or_page( $post_id, $post, $update ) {

        /*
         * Ignore on:
         * - autosave,
         * - auto-draft,
         * - contact form save,
         * - revision
         */
        if ( is_admin() ) {
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return( false );
            if ( empty( $post_id ) ) return( false );
            if ( $post->post_status == 'auto-draft' ) return( false );
            if ( false !== wp_is_post_revision( $post_id ) ) return( false );        // If this is just a revision, don't send the email.
            if ( $post->post_type == 'wpcf7_contact_form' )  return( false );
        }

        $template_fields = $this->get_template_fields();

        $template_fields['post-date'] = $post->post_date;
        $template_fields['post-status'] = $post->post_status;
        $template_fields['post-content'] = $post->post_content;
        $template_fields['post-title'] = $post->post_title;
        $template_fields['post-type'] = $post->post_type;
        $template_fields['post-id'] = $post->ID;
        $template_fields['post-name'] = $post->post_name;

        if ( $this->log ) $this->generate_log( 'post-page-saved', $template_fields );

        $args = array(
            $template_fields,
            $post
        );

        // POST META
        $active_options = true;

        $options = get_exopite_sof_option( $this->plugin_name );

        if ( ! isset( $options['metabox'] ) || $options['metabox'] == 'yes' ) {

            $post_meta = get_post_meta( $post_id, $this->plugin_name . '-meta' );
            $item['email_smtp_override'] = $post_meta[0]['post_email_smtp_override'];

            if ( isset( $post_meta[0]['active_options'] ) && $post_meta[0]['active_options'] == 'no' ) {

                $active_options = false;

            }

            if ( isset( $post_meta[0]['active_post'] ) && $post_meta[0]['active_post'] == 'yes' ) {

                $item_meta = array();
                $item_meta['telegram_recipients'] = $post_meta[0]['post_telegram_recipients'];
                $item_meta['email_recipients'] = $post_meta[0]['post_email_recipients'];
                $item_meta['email_recipients_additional'] = $post_meta[0]['post_email_recipients_additional'];

                foreach ( $post_meta[0]['post_alert_type'] as $key ) {

                    $item_meta['email_type'] = $key;
                    $template_fields['alert-type'] = $key;

                    foreach ( $this->send_types as $send_type ) {

                        if ( ( $post->post_date == $post->post_modified && $item[$key . '_type'] == 'post-new' ) ||
                             ( $post->post_status == 'trash' && $item[$key . '_type'] == 'post-delete' ) ||
                             ( $post->post_status != 'trash' && $post->post_date != $post->post_modified && $key == 'post-update' )
                           ){

                            call_user_func_array( array( $this, 'send_message_' . $send_type ), array( $item_meta, $this->generate_template( $template_fields, $post_meta[0]['post_template'] ) ) );

                        }

                    }

                }

            }

        }

        // PLUGIN MENU
        if ( $active_options ) {

            $this->loop_options( function( $template_fields, $post, $key, $item ) {

                if ( ( $post->post_date == $post->post_modified && $item[$key . '_type'] == 'post-new' ) ||
                     ( $post->post_status == 'trash' && $item[$key . '_type'] == 'post-delete' ) ||
                     ( $post->post_status != 'trash' && $post->post_date != $post->post_modified && $item[$key . '_type'] == 'post-update' )
                   ){

                    $this->do_action( $template_fields, $key, $item );

                }

            }, $args );

        }

    }

}
