<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sim_to_shop
 * @subpackage Sim_to_shop/includes
 * @author     2WS Technologies <contact@ardary-sms.com>
 */
if (WP_DEBUG) {
    error_log("begin class-sim-to-shop");
}

class Sim_To_Shop {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Sim_To_Shop_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $sim_to_shop    The string used to uniquely identify this plugin.
     */
    protected $sim_to_shop;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (WP_DEBUG) {
            error_log("class-sim-to-shop _contstruct");
        }
        $this->sim_to_shop = 'insim';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes' . DIRECTORY_SEPARATOR . 'sim-to-shop-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes' . DIRECTORY_SEPARATOR . 'sim-to-shop-i18n.php';

        require_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woocommerce' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'class-wc-settings-page.php';

        require_once ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'woocommerce' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-wc-countries.php';

        include_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-messages.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public' . DIRECTORY_SEPARATOR . 'sim-to-shop-public.php';


        require_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-api.php';

        include_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-settings.php';
        include_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-api.php';
        include_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-messages.php';
        include_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-send-tab.php';
        include_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-history-tab.php';
        include_once plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR . 'sim-to-shop-news.php';

        $this->loader = new Sim_To_Shop_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Sim_To_Shop_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Sim_To_Shop_i18n();
        $plugin_i18n->set_domain($this->get_sim_to_shop());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        if (WP_DEBUG) {
            error_log("define_admin_hooks");
        }
        $plugin_admin = new Sim_To_Shop_Admin($this->get_sim_to_shop(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_action('admin_menu', $plugin_admin, 'sim_to_shop_menu', 60);

        //add action for sending SMS (it is possible that function is disable in function of the configuration)
        //'action_create_account' after account creation
        $this->loader->add_action('woocommerce_created_customer', $plugin_admin, 'action_create_account', 10, 1);
        //'action_validate_order'
        //$this->loader->add_action('woocommerce_new_order', $plugin_admin, 'action_validate_order', 10, 1);
        $this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_admin, 'action_validate_order', 10, 2);
        //'action_send_message' - send when a comment is added
        //not this one : $this->loader->add_action( 'add_meta_boxes_comment', $plugin_admin,'action_send_message',10,1);
        $this->loader->add_action('wp_insert_comment', $plugin_admin, 'action_send_message', 10, 2);
        //'action_order_return'
        //'action_update_quantity' only if 'woocommerce_notify_low_stock' is set
        $this->loader->add_action('woocommerce_low_stock', $plugin_admin, 'action_update_quantity', 10, 1);
        //'action_admin_alert' - specific hook call every time a sms is send in send function.
        //'action_daily_report' - hook call with wp cron
        $this->loader->add_action('sim_to_shop_event_daily_hook', $plugin_admin, 'action_daily_report');

        $this->loader->add_action('password_reset', $plugin_admin, 'action_password_renew', 10, 2);


        //'action_order_status_update'
        $this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'action_order_status_update', 10, 3);
        //$this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'action_daily_report');
        //ajax hook
        $this->loader->add_action('wp_ajax_addRecipient', $plugin_admin, 'action_add_recipient');
        $this->loader->add_action('wp_ajax_transmitOWS', $plugin_admin, 'action_transmit_ows');
        $this->loader->add_action('wp_ajax_delRecipient', $plugin_admin, 'action_del_recipient');
        $this->loader->add_action('wp_ajax_filter', $plugin_admin, 'action_filter');
        $this->loader->add_action('wp_ajax_addRecipientsFromQuery', $plugin_admin, 'action_add_recipients_from_query');
        $this->loader->add_action('wp_ajax_countRecipientFromQuery', $plugin_admin, 'action_count_recipients_from_query');
        $this->loader->add_action('wp_ajax__ajax_fetch_custom_list', $plugin_admin, '_ajax_fetch_custom_list_callback');
        $this->loader->add_action('wp_ajax__ajax_fetch_recipient_list', $plugin_admin, '_ajax_fetch_recipient_list_callback');

        //#2
        $this->loader->add_action('wp_ajax_filterUser', $plugin_admin, 'action_filter_user');
        $this->loader->add_action('wp_ajax_addRecipientsFromRole', $plugin_admin, 'action_add_recipients_from_role');
        
        //last login
        $this->loader->add_action('wp_login', $plugin_admin, 'action_wp_login');

        //settings link on plugin page
        $this->loader->add_filter("plugin_action_links", $plugin_admin, 'settings_link', 10, 4);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Sim_To_Shop_Public($this->get_sim_to_shop(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
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
    public function get_sim_to_shop() {
        return $this->sim_to_shop;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Sim_To_Shop_Loader    Orchestrates the hooks of the plugin.
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
