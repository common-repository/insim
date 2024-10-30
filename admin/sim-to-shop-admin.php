<?php
/*
 * Copyright (C) 2022 sim_to_shop
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://ardary-solo.com
 * @since      1.0.0
 *
 * @package    Sim_to_shop
 * @subpackage Sim_to_shop/admin
 */
class Sim_To_Shop_Admin {

    private static $sim_to_shop_admin;

    public static function get_instance() {
        global $sim_to_shop_admin;
        if (is_null($sim_to_shop_admin)) {
            $sim_to_shop_admin = new Sim_To_Shop_API();
        }
        return $sim_to_shop_admin;
    }

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $sim_to_shop    The ID of this plugin.
     */
    private $sim_to_shop;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    
    /**
     * Admin messages with their title
     */
    public $admin_config;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $sim_to_shop       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($sim_to_shop, $version) {
        global $sim_to_shop_admin;

        $this->sim_to_shop = $sim_to_shop;
        $this->version = $version;

        $sim_to_shop_admin = $this;

        //initialisation of possible admin message
        $this->admin_config = array(
            'action_create_account' => __('New account', 'insim'), //don't suppress                
            //'action_send_message' => __('Message received', 'insim'),
            'action_validate_order' => __('Order for validation', 'insim'),
            //'action_order_return' => __('Order return', 'insim'),
            'action_update_quantity' => __('Stockout warning', 'insim'),
			//'action_test_sms' => __('Send test sms', 'insim'),
            //'action_admin_alert' => __('Low SMS balance', 'insim'),
            //'action_daily_report' => __('Daily report', 'insim')
        );

        $this->customer_config = array(
            'action_create_account' => __('Welcome message', 'insim'),
            //'action_password_renew' => __('Password recovery', 'insim'),
            //'action_send_message' => __('Message received', 'insim'),
            'action_validate_order' => __('Order confirmation', 'insim'),
            'action_order_status_update' => __('Status update', 'insim'));
    }

    /**
     * Register the stylesheets for the Dashboard.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->sim_to_shop, plugin_dir_url(__FILE__) . 'css/font.css', array(), $this->version, 'all');
        $position = strpos(get_current_screen()->id,'woocommerce_page_sim-to-shop');
        if ($position === false ) {
        } else {
            wp_enqueue_style($this->sim_to_shop, plugin_dir_url(__FILE__) . 'css/sim-to-shop-admin.css', array(), $this->version, 'all');
            $jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
            // wp_enqueue_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), WC_VERSION);
            //icon of woocommerce
            wp_enqueue_style(ABSPATH . 'wp-content/plugin/woocommerce.assets/css/mixins.css');
        }        
    }

    /**
     * Register the JavaScript for the dashboard.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script($this->sim_to_shop, plugin_dir_url(__FILE__) . 'js/sim-to-shop-admin.js', array('jquery'), $this->version, false);
        
        wp_enqueue_script( 'jquery-ui-datepicker' );
        

        wp_register_style( 'jquery-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui.css');
        
        wp_enqueue_style( 'jquery-ui' );  
    }    

    
    public function sim_to_shop_menu() {
        if (WP_DEBUG) {
                error_log("add_submenu_page: ");
            }
        $simtoshop_page = add_submenu_page('woocommerce', __('Ardary inSIM', 'insim'), __('Ardary inSIM', 'insim'), 'manage_woocommerce', 'insim', array($this, 'output'));
        
        }

    
    public static function output() {
        $current_tab = !empty($_REQUEST['tab']) ? sanitize_title($_REQUEST['tab']) : 'settings';
        $current_action = !empty($_REQUEST['action']) ? sanitize_title($_REQUEST['action']) : '';
        if (WP_DEBUG)
            error_log(print_r($_REQUEST, true));
        include_once( 'partials/html-admin-page.php' );
    }

    public static function sim_to_shop_news() {
        $news = new Sim_To_Shop_News();
        $news->output();
    }

    public static function sim_to_shop_settings() {
        $settings = new Sim_To_Shop_Settings();
        $settings->output();
    }

    /**
     * Get the key of a "hook".
     * 
     * @param type $hookId
     * @param type $b_admin
     * @param type $params
     * @return type
     */
    public function _get_hook_key($hookId, $b_admin = false, $params = null) {
        if (is_array($params) && key_exists('new_status', $params)) {
            return 'sim_to_shop_txt_' . $hookId . '_wc-' . $params['new_status'] . ($b_admin ? '_admin' : '');
        }
        return 'sim_to_shop_txt_' . $hookId . ($b_admin ? '_admin' : '');
    }

    /**
     * Return if the "hook" is valid or not.
     * 
     * @param type $hookId
     * @param type $b_admin
     * @param type $params
     * @return type
     */
    public function _get_isactive_hook_key($hookId, $b_admin = false, $params = null) {
        if (is_array($params) && key_exists('new_status', $params)) {
            return 'sim_to_shop_isactive_' . $hookId . '_wc-' . $params['new_status'] . ($b_admin ? '_admin' : '');
        }
        return 'sim_to_shop_isactive_' . $hookId . (($b_admin) ? '_admin' : '');
    }


    public function settings_link($links,$file) {
        $plugin_file = 'insim/insim.php';
	if ( $file == $plugin_file ) {
            	$settings_link = '<a href="' .
			admin_url( 'admin.php?page=insim&tab=settings' ) . '">' .
			__('Settings','insim') . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
    }

    /**
     * Create account hook for admin.
     * @param type $customer_id
     */
    public function action_create_account($customer_id) {
        if (WP_DEBUG)
            error_log('action_create_account -BEGIN');
        if (WP_DEBUG)
            error_log('action_create_account - $_POST' . print_r($_POST, true) . ' customer_id ' . $customer_id);
        //send only if the check box create account is selected on the form
        if (isset($_POST['createaccount']) && wc_clean($_POST['createaccount'] == 1)) {
            $sim_to_shop_api = Sim_To_Shop_API::get_instance();
            $sim_to_shop_api->send('action_create_account', array('customer_id' => $customer_id));
        } else {
            if (WP_DEBUG)
                error_log('action_create_account - No account creation');
        }
    }

    /**
     * Comment add hook for admin : when a user had a comment
     * @param type $comment
     */
    public function action_send_message($id, $comment) {
        if (WP_DEBUG)
            error_log('action_send_message -BEGIN');
        if (WP_DEBUG)
            error_log('action_send_message - $_POST' . print_r($_POST, true));
        if ($comment->comment_author != 'WooCommerce') {
            $sim_to_shop_api = Sim_To_Shop_API::get_instance();
            $sim_to_shop_api->send('action_send_message', array('comment' => $comment, 'id' => $id));
        }
    }

    public function action_validate_order($order_id) {
        if (WP_DEBUG)
            error_log('action_validate_order -BEGIN');
        if (WP_DEBUG)
            error_log('action_validate_order - $_POST' . print_r($_POST, true));
        $sim_to_shop_api = Sim_To_Shop_API::get_instance();
        $sim_to_shop_api->send('action_validate_order', array('order_id' => $order_id));
    }

    //'action_order_status_update'
    //do_action( 'woocommerce_order_status_changed', $this->id, $old_status, $new_status );
    public function action_order_status_update($order_id, $old_status, $new_status) {
        if (WP_DEBUG)
            error_log('action_order_status_update -BEGIN');
        if (WP_DEBUG)
            error_log('action_order_status_update - $_POST' . print_r($_POST, true));
        $sim_to_shop_api = Sim_To_Shop_API::get_instance();
        $sim_to_shop_api->send('action_order_status_update', array('order_id' => $order_id, 'old_status' => $old_status, 'new_status' => $new_status));
    }

    public function action_password_renew($user, $new_pass) {
        if (WP_DEBUG)
            error_log('action_password_renew -BEGIN');
        if (WP_DEBUG)
            error_log('action_password_renew - $_POST' . print_r($_POST, true));
        $sim_to_shop_api = Sim_To_Shop_API::get_instance();
        $sim_to_shop_api->send('action_password_renew', array('user' => $user, 'new_pass' => $new_pass));
    }

    public function action_order_return() {
        if (WP_DEBUG)
            error_log('action_send_message -BEGIN');
        if (WP_DEBUG)
            error_log('action_send_message - $_POST' . print_r($_POST, true));
        $sim_to_shop_api = Sim_To_Shop_API::get_instance();
        $sim_to_shop_api->send('action_send_message', array('customer_id' => $customer_id));
    }

    public function action_update_quantity($product) {
        if (WP_DEBUG)
            error_log('action_update_quantity -BEGIN');
        if (WP_DEBUG)
            error_log('action_update_quantity - $_POST' . print_r($_POST, true));
        $sim_to_shop_api = Sim_To_Shop_API::get_instance();
        $sim_to_shop_api->send('action_update_quantity', array('product' => $product));
    }

    public function action_test_sms() {
        if (WP_DEBUG)
            error_log('action_test_sms -BEGIN');
        if (WP_DEBUG)
            error_log('action_test_sms - $_POST' . print_r($_POST, true));
        $sim_to_shop_api = Sim_To_Shop_API::get_instance();
        $test_sms = $sim_to_shop_api->send('action_test_sms', array());
        
        return $test_sms['admin'];
    }

    public function action_wp_login($login) {
        //TODO global $user_ID;
        $user = get_user_by('login', $login);
        update_user_meta($user->ID, 'last_login', date('Y:m:d H:i:s'));
    }

    public function action_admin_alert() {
        if (WP_DEBUG)
            error_log('action_admin_alert -BEGIN');
        if (WP_DEBUG)
            error_log('action_admin_alert - $_POST' . print_r($_POST, true));
        $sim_to_shop_api = Sim_To_Shop_API::get_instance();
        $sim_to_shop_api->send('action_admin_alert', array());
    }

    /**
     * Send a daily report if needed
     */
    public function action_daily_report() {
        if (WP_DEBUG)
            error_log('action_daily_report -BEGIN');
        $sim_to_shop_api = Sim_To_Shop_API::get_instance();
        $sim_to_shop_api->send('action_daily_report', array());
    }

    /* Handle ajax call */

    /**
     * Ajax for add_recipient
     */
    function action_add_recipient() {
        Sim_To_Shop_Send_Tab::_ajax_process_addRecipient();
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    /**
     * Ajax for transmit to simtoshop Web Service
     */
    function action_transmit_ows() {
        Sim_To_Shop_Send_Tab::_ajax_process_transmitOWS();
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    function action_del_recipient() {
        Sim_To_Shop_Send_Tab::_ajax_process_delRecipient();
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    function action_add_recipients_from_query() {
        Sim_To_Shop_Send_Tab::_ajax_process_addRecipientsFromQuery();
        wp_die();
    }

    function action_count_recipients_from_query() {
        Sim_To_Shop_Send_Tab::_ajax_process_countRecipientsFromQuery();
        wp_die();
    }

    /**
     * Ajax list
     */
    function _ajax_fetch_custom_list_callback() {
        if (isset($_REQUEST['list']) && $_REQUEST['list'] == 'Sim_To_Shop_Recipient_List') {
            if (isset($_REQUEST['id_sendsms_campaign'])) {
                if (WP_DEBUG) {
                    error_log("_ajax_fetch_custom_list_callback()" . print_r($_REQUEST, true));
                }
                $wp_list_table = new Sim_To_Shop_Recipient_List(array("campaign" => new Sim_To_Shop_Campaign_Model((intval($_REQUEST['id_sendsms_campaign'])))));
                $wp_list_table->ajax_response();
            } else {
                wp_send_json(array("error" => __("id_sendsms_campaign is not set.", "sim-to-shop")));
            }
        } else {
            $wp_list_table = new Sim_To_Shop_Campaign_List(array("status" => array(3, 4, 5)));
            $wp_list_table->ajax_response();
        }
    }

    function action_filter() {
        Sim_To_Shop_Send_Tab::_ajax_process_filter();
        wp_die();
    }

    //#2
    function action_filter_user() {
        Sim_To_Shop_Send_Tab::_ajax_process_filter_user();
        wp_die();
    }

    function action_add_recipients_from_role() {
        Sim_To_Shop_Send_Tab::_ajax_process_addRecipientsFromRole();
        wp_die();
    }

    public static function Sim_To_Shop_messages() {
        $messages = new Sim_To_Shop_Messages();
        echo $messages->getBody();
    }

    /**
     * Display campaign tab
     */
    public static function Sim_To_Shop_campaigns() {
        $send_tab = new Sim_To_Shop_Send_Tab();
        echo $send_tab->get_body();
    }

    /**
     * Display history tab
     */
    public static function Sim_To_Shop_history() {
        $history_tab = new Sim_To_Shop_History_Tab();
        echo $history_tab->get_body();
    }

    /**
     * Load the settings class
     * @param  array $settings
     * @return array
     */
    public function load_settings_class($settings) {
        $settings[] = include 'class-sim-to-shop-settings.php';
        return $settings;
    }

    /**
     * Handles output of report
     */
    public static function status_report() {
        //include_once( 'views/html-admin-page-status-report.php' );
    }

    public function get_form_url() {
        $uri = sanitize_url($_SERVER['REQUEST_URI']);
        $pos = strpos($_SERVER['REQUEST_URI'], '&action=');
        if ($pos !== false)
            $uri = substr(sanitize_url($_SERVER['REQUEST_URI']), 0, $pos);
        return esc_url_raw($uri);
    }

    public function get_status($status) {
        switch ($status) {
            case 0:
                return __('In construction', 'insim');
            case 3:
                return __('Sent', 'insim');
            case 5:
                return __('Error', 'insim');
            default:
                break;
        }
    }

   
    /**
     * TODO englis message
     * Return error messages
     * @param type $code
     * @return type
     */
    public function get_error_SMS($code) {
        error_log("Test" . $code);
        if (isset($code) && array_key_exists(intval($code), $GLOBALS['errors'])) {
            return $GLOBALS['errors'][intval($code)];
        }
        return __('Error unknown', 'insim') . " $code";
    }

}
