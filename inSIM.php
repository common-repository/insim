<?php
/**
 * inSIM.php
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ardary-insim.com
 * @since             0.0.1
 * @package           Sim To Shop
 *
 * @wordpress-plugin
 * Plugin Name:       inSIM
 * Plugin URI:        https://ardary-insim.com/
 * Description:       This plugin allows to create an automation or a campaign in order to send sms to a list of recipients as you can do with emails and newsletters.
 * Version:           1.5.0
 * Author:            2WS Technologies
 * Author URI:        https://ardary-insim.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       insim
 * Domain Path:       /languages
 */

 if (!defined('WPINC')) {
    die;
}


if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    return;

if ( ! class_exists( 'WC_API_Customers' ) ) {
    $WOOCOMMERCE_FOLDER = ABSPATH.'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'woocommerce'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'legacy'.DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'v3'.DIRECTORY_SEPARATOR;
    require_once $WOOCOMMERCE_FOLDER.'class-wc-api-resource.php';
    require_once $WOOCOMMERCE_FOLDER.'class-wc-api-customers.php';
    require_once ABSPATH.'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'woocommerce'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'class-wc-checkout.php';
}

add_action( 'woocommerce_init', 'get_subs_type' );

function get_subs_type(){
    if (  null != get_option('solo_sms_key') && null != get_option('solo_sms_email') ){
        $urlAdd = "https://ardary-sms.com/api/get_license_details.php";
        $dataAdd = [
                'access_key' => get_option('solo_sms_key'),
                'mail' =>  get_option('solo_sms_email') 
           
        ];
            $args = array(
                'body'        => json_encode($dataAdd),
                'timeout'     => 45,
                'sslverify'   => false,
            );

            $res = json_decode(wp_remote_post( $urlAdd, $args )['body']);
            update_option ('is_premium', false);
            update_option ('subsc_type', 'not_set');

            if ( isset($res->data) && $res->data != "notExist"){
                if ($res->data->is_premium) {
                    update_option ('is_premium', false);
                    update_option('is_team', $res->data->in_team);
                }
                update_option ('subsc_type', sanitize_text_field($res->data->subscription_type));
            }

    }
   

}


add_action('wp_ajax_get-access', 'inSIM_get_access');
add_action('wp_ajax_nopriv_get-access', 'inSIM_get_access');

function inSIM_get_access (){

    if ((null != get_option('solo_sms_email')) && ('' != get_option('solo_sms_email')) && (null != get_option('solo_sms_key')) && ('' != get_option('solo_sms_key'))) {
        $response = json_encode(array('email' => get_option('solo_sms_email'), 'key' => get_option('solo_sms_key')));
    } else {
        $response = 'false';
    }
    
    echo $response;
    die();
}

add_action('wp_ajax_change-setting', 'inSIM_change_setting_position');
add_action('wp_ajax_nopriv_change-setting', 'inSIM_change_setting_position');

function inSIM_change_setting_position (){
    if(isset($_POST['op'])){
        update_option ('setting_change', true);
    }
}


add_action('wp_ajax_get-data', 'inSIM_refresh_page');
add_action('wp_ajax_nopriv_get-data', 'inSIM_refresh_page');

function inSIM_refresh_page (){

    if((isset($_POST['email']))&&(isset($_POST['key']))){
    $sms_email = sanitize_email(($_POST["email"]));
    $sms_key = sanitize_user($_POST["key"]);
    update_option ('solo_sms_email', $sms_email);
    update_option ('solo_sms_key', $sms_key);

    $urlAdd = "https://www.ardary-sms.com/api/add_shop_to_crm.php";
    $dataAdd = [
        'header' => [
            'key' => get_option('solo_sms_key'),
            'email' =>  get_option('solo_sms_email'),
            'frompluguin' => true
        ],
        'versionPlug' =>'1.5.0',
        'shop_name' =>get_bloginfo(),
        'shop_url' =>home_url(),
        'domain' => get_site_url(),
        'type' => 'e-shop',
        'name' => 'wooc',
        'crm_url_profile' =>admin_url('user-edit.php?user_id=[id_customer]'),
        'crm_url_orders' =>admin_url('edit.php?post_status=all&post_type=shop_order&_customer_user=[id_customer]')
    ];
   

        $args = array(
            'body'        => json_encode($dataAdd),
            'timeout'     => 45,
            'sslverify'   => false,
        );

        $res = wp_remote_post( $urlAdd, $args );

    //Add client to wooCommerce intégration table
        $versionB = 0;
            if ( ! class_exists( 'WooCommerce' ) ) {
                //woocommerce is not activated or installed
                $versionB = -1;
            } else {
                $versionB = WC_VERSION;
            }
        $isMulti = is_multisite() ? 1 : -1;
        $urlAddInteg = "https://www.ardary-sms.com/api/cms_integration.php";
        $dataAddInteg = [
            'shop_name' =>get_bloginfo(),
            'shop_url' =>home_url(),
            'type' => 'e-shop',
            'name' => 'wooc',
            'admin_mail' => get_bloginfo('admin_email'),
            'admin_phone' => get_user_meta(get_current_user_id(),'phone_number',true),
            'plugin_version' => '1.5.0',
            'shop_version' => $versionB,
            'is_multi' => $isMulti
            
        ];
   

        $args = array(
            'body'        => json_encode(array( 'data' =>json_encode($dataAddInteg))),
            'timeout'     => 45,
            'sslverify'   => false,
        );

        $res = wp_remote_post( $urlAddInteg, $args );

        inSIM_import_contacts();

    return true;
    }
    return -1;
   

}



function inSIM_action_delete_user2( $user_id ) {
    $id = array('id' => $user_id);
    $id = json_encode(array('object' => $id));
    

    $url = "https://www.ardary-sms.com/api/delete_contact_wooc.php";
    $data = [
        'header' => [
            'key' => get_option('solo_sms_key'),
            'frompluguin' => true
        ],
        'contacts' => [$id]

    ];

    $args = array(
        'body'        => json_encode(array( 'data' =>base64_encode(gzcompress(json_encode($data))))),
        'timeout'     => 45,
        'sslverify'   => false,
    );

    $res = wp_remote_post( $url, $args );
    
 
}
add_action( 'delete_user', 'inSIM_action_delete_user2' );

function inSIM_activate_sim_to_shop() {


    global $wpdb;
    global $charset_collate;
    $table_name = $wpdb->prefix . 'simtoshop_campaign';
     $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id_sendsms_campaign int unsigned NOT NULL auto_increment,
            `ticket` varchar(255) NOT NULL,
            `title` varchar(255) default NULL,
            `status` tinyint(1) unsigned NOT NULL default 0 COMMENT '0=in construction, 1=in transfert, 2=waiting for validation, 3=sent, 4=canceled, 5=error',
            `error_code` varchar(4) default NULL,
            `message` text default NULL,
            `nb_recipients` int unsigned NOT NULL default 0,
            `nb_sms` int unsigned NOT NULL default 0,
            `price` double(5,3) NOT NULL default 0,
            `event` varchar(64) NOT NULL default 'sendsmsFree',
            `paid_by_customer` tinyint(1) unsigned NOT NULL default 0,
            `simulation` tinyint(1) unsigned NOT NULL default 0,
            `date_send` datetime default NULL,
            `date_transmitted` datetime default NULL,
            `date_validation` datetime default NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            `isteam` tinyint(1) unsigned NOT NULL default 0,
            PRIMARY KEY  (`id_sendsms_campaign`)
    )$charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
     dbDelta( $sql );

       
     $table_name = $wpdb->prefix . 'simtoshop_recipient';
     $sql = "CREATE TABLE IF NOT EXISTS $table_name (
             `id_sendsms_recipient` int unsigned NOT NULL auto_increment,
             `id_sendsms_campaign` int unsigned NOT NULL,
             `id_customer` int unsigned default NULL,
             `firstname` varchar(32) default NULL,
             `lastname` varchar(100) default NULL,
             `phone` varchar(16) default NULL,
             `iso_country` char(2) NULL,
             `transmitted` tinyint(1) unsigned NOT NULL default 0,
             `price` double(5,3) NOT NULL default 0,
             `nb_sms` int unsigned NOT NULL default 0,
             `status` int unsigned NOT NULL DEFAULT 0,
             `ticket` varchar(255) default NULL,
             `date_add` datetime NOT NULL,
             `date_upd` datetime NOT NULL,
             PRIMARY KEY  (`id_sendsms_recipient`),
             UNIQUE `index_unique_phone` (`id_sendsms_campaign` , `phone`)
     )$charset_collate;";
     dbDelta($sql);

     $charset_collate;
     $table_name = $wpdb->prefix . 'simtoshop_phone_prefix';

     $sql = "CREATE TABLE IF NOT EXISTS $table_name (
         `iso_code` varchar(3) NOT NULL,
         `prefix` int(10) unsigned DEFAULT NULL,
         PRIMARY KEY  (`iso_code`)
       )$charset_collate;";

     
     dbDelta($sql);

     $wpdb->query("INSERT IGNORE INTO " . $wpdb->prefix . "simtoshop_phone_prefix (iso_code, prefix) VALUES
         ('AD', 376),('AE', 971),('AF', 93),('AG', 1268),('AI', 1264),('AL', 355),('AM', 374),('AN', 599),('AO', 244),
         ('AQ', 672),('AR', 54),('AS', 1684),('AT', 43),('AU', 61),('AW', 297),('AX', NULL),('AZ', 994),('BA', 387),
         ('BB', 1246),('BD', 880),('BE', 32),('BF', 226),('BG', 359),('BH', 973),('BI', 257),('BJ', 229),('BL', 590),('BM', 1441),
         ('BN', 673),('BO', 591),('BR', 55),('BS', 1242),('BT', 975),('BV', NULL),('BW', 267),('BY', 375),('BZ', 501),
         ('CA', 1),('CC', 61),('CD', 242),('CF', 236),('CG', 243),('CH', 41),('CI', 225),('CK', 682),('CL', 56),('CM', 237),
         ('CN', 86),('CO', 57),('CR', 506),('CU', 53),('CV', 238),('CX', 61),('CY', 357),('CZ', 420),('DE', 49),('DJ', 253),
         ('DK', 45),('DM', 1767),('DO', 1809),('DZ', 213),('EC', 593),('EE', 372),('EG', 20),('EH', NULL),('ER', 291),('ES', 34),
         ('ET', 251),('FI', 358),('FJ', 679),('FK', 500),('FM', 691),('FO', 298),('FR', 33),('GA', 241),('GB', 44),('GD', 1473),
         ('GE', 995),('GF', 594),('GG', NULL),('GH', 233),('GI', 350),('GL', 299),('GM', 220),('GN', 224),('GP', 590),('GQ', 240),
         ('GR', 30),('GS', NULL),('GT', 502),('GU', 1671),('GW', 245),('GY', 592),('HK', 852),('HM', NULL),('HN', 504),('HR', 385),
         ('HT', 509),('HU', 36),('ID', 62),('IE', 353),('IL', 972),('IM', 44),('IN', 91),('IO', 1284),('IQ', 964),('IR', 98),
         ('IS', 354),('IT', 39),('JE', 44),('JM', 1876),('JO', 962),('JP', 81),('KE', 254),('KG', 996),('KH', 855),('KI', 686),
         ('KM', 269),('KN', 1869),('KP', 850),('KR', 82),('KW', 965),('KY', 1345),('KZ', 7),('LA', 856),('LB', 961),('LC', 1758),
         ('LI', 423),('LK', 94),('LR', 231),('LS', 266),('LT', 370),('LU', 352),('LV', 371),('LY', 218),('MA', 212),('MC', 377),
         ('MD', 373),('ME', 382),('MF', 1599),('MG', 261),('MH', 692),('MK', 389),('ML', 223),('MM', 95),('MN', 976),('MO', 853),
         ('MP', 1670),('MQ', 596),('MR', 222),('MS', 1664),('MT', 356),('MU', 230),('MV', 960),('MW', 265),('MX', 52),('MY', 60),
         ('MZ', 258),('NA', 264),('NC', 687),('NE', 227),('NF', 672),('NG', 234),('NI', 505),('NL', 31),('NO', 47),('NP', 977),
         ('NR', 674),('NU', 683),('NZ', 64),('OM', 968),('PA', 507),('PE', 51),('PF', 689),('PG', 675),('PH', 63),('PK', 92),
         ('PL', 48),('PM', 508),('PN', 870),('PR', 1),('PS', NULL),('PT', 351),('PW', 680),('PY', 595),('QA', 974),('RE', 262),
         ('RO', 40),('RS', 381),('RU', 7),('RW', 250),('SA', 966),('SB', 677),('SC', 248),('SD', 249),('SE', 46),('SG', 65),
         ('SI', 386),('SJ', NULL),('SK', 421),('SL', 232),('SM', 378),('SN', 221),('SO', 252),('SR', 597),('ST', 239),('SV', 503),
         ('SY', 963),('SZ', 268),('TC', 1649),('TD', 235),('TF', NULL),('TG', 228),('TH', 66),('TJ', 992),('TK', 690),('TL', 670),
         ('TM', 993),('TN', 216),('TO', 676),('TR', 90),('TT', 1868),('TV', 688),('TW', 886),('TZ', 255),('UA', 380),('UG', 256),
         ('US', 1),('UY', 598),('UZ', 998),('VA', 379),('VC', 1784),('VE', 58),('VG', 1284),('VI', 1340),('VN', 84),('VU', 678),
         ('WF', 681),('WS', 685),('YE', 967),('YT', 262),('ZA', 27),('ZM', 260),('ZW', 263);"
     );

     
    $versionB = 0;
        if ( ! class_exists( 'WooCommerce' ) ) {
            //woocommerce is not activated or installed
            $versionB = -1;
        } else {
            $versionB = WC_VERSION;
        }
    $isMulti = is_multisite() ? 1 : -1;
 
     $url = "https://www.ardary-sms.com/api/cms_installation.php";
     $data = [
        'admin_mail' => get_bloginfo('admin_email'),
        'admin_phone' => get_user_meta(get_current_user_id(),'phone_number',true),
        'shop_url' => home_url(),
        'plugin_version' => '1.5.0',
        'type' => 'e-shop',
        'name' => 'wooc',
        'shop_version' => $versionB,
        'is_multi' => $isMulti
 
     ];
 
     $args = array(
         'body'        => json_encode(array( 'data' =>json_encode($data))),
         'timeout'     => 45,
         'sslverify'   => false,
     );
 
     $res = wp_remote_post( $url, $args );

     
    require_once plugin_dir_path(__FILE__) . 'includes'.DIRECTORY_SEPARATOR.'sim-to-shop-activator.php';
    Sim_To_Shop_Activator::activate();
}


function inSIM_deactivate_sim_to_shop() {
    
    $versionB = 0;
        if ( ! class_exists( 'WooCommerce' ) ) {
            //woocommerce is not activated or installed
            $versionB = -1;
        } else {
            $versionB = WC_VERSION;
        }
    $isMulti = is_multisite() ? 1 : -1;
 
     $url = "https://www.ardary-sms.com/api/cms_installation.php";
     $data = [
        'admin_mail' => get_bloginfo('admin_email'),
        'admin_phone' => get_user_meta(get_current_user_id(),'phone_number',true),
        'shop_url' => home_url(),
        'plugin_version' => '1.5.0',
        'type' => 'e-shop',
        'name' => 'wooc',
        'shop_version' => $versionB,
        'is_multi' => $isMulti,
        'deleted' => true
 
     ];
 
     $args = array(
         'body'        => json_encode(array( 'data' =>json_encode($data))),
         'timeout'     => 45,
         'sslverify'   => false,
     );
 
     $res = wp_remote_post( $url, $args );
     //print_r(wp_remote_retrieve_body($res));die();
    require_once plugin_dir_path(__FILE__) . 'includes'.DIRECTORY_SEPARATOR.'sim-to-shop-deactivator.php';
    Sim_To_Shop_Deactivator::deactivate();
    delete_option('solo_sms_key');
    delete_option('solo_sms_email');
    delete_option('is_premium');
    delete_option('subsc_type');
    delete_option('setting_change');
    delete_option('phone_admin');
}

register_activation_hook(__FILE__, 'inSIM_activate_sim_to_shop');
register_deactivation_hook(__FILE__, 'inSIM_deactivate_sim_to_shop');


require plugin_dir_path(__FILE__) . 'includes'.DIRECTORY_SEPARATOR.'sim-to-shop.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function inSIM_run_sim_to_shop() {
    $plugin = new Sim_To_Shop();
    $plugin->run();
}

inSIM_run_sim_to_shop();





add_action('wp_ajax_test_sms', 'inSIM_test_sms');

function inSIM_test_sms() {
    global $wpdb;

    $sim_to_shop_api = Sim_To_Shop_Admin::get_instance();
    $result = $sim_to_shop_api->action_test_sms();
    if ($result === true) {
       echo __("SMS has been sent successfully on your mobile.", 'insim');
    } else if ($result === false) {
        echo __("Failed to send SMS.", 'insim');
     } else if (isset($result['error']) && $result['error'] === true) {
        echo __("Failed to send SMS.\nError_code: ".$result['error_code']."\nError details: ".$result['error_details'], 'insim');
     } else  {
        echo __("Failed to send SMS.\nError_code: Unknow Error code", 'insim');         
    }
    exit();
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'inSIM_Sim_to_shop_plugin_links');

function inSIM_Sim_to_shop_plugin_links( $links ) {

$plugin_links = array(
'<a href="' . admin_url( 'admin.php?page=insim&tab=settings' ) . '">' . __( 'Settings', 'insim' ) . '</a>'
);

return array_merge( $plugin_links, $links );
}

    
add_action( 'woocommerce_new_order', 'inSIM_action_woocommerce_new_order2', 10, 1); 
        




function inSIM_import_contacts(){

    $start = isset($_GET['max']) && $_GET['max'] != "" ? sanitize_key($_GET['max']) : 0;
    //$indicator = 'customers';
    global $wpdb;            
       
            $ord =$wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'shop_order'", ARRAY_A );

            $address =[];

            $od =[];
         

            foreach ($ord as $key => $value){
                

                $aux = wc_get_order ($value['ID']);
                if ($aux != null && $aux != ''){

                    $od['id_customer'] = null !== $aux->get_customer_id() ? $aux->get_customer_id() : -1;

                    $od['firstname'] = isset($aux->billing_first_name) &&  $aux->billing_first_name !="" ? $aux->billing_first_name : "null";

                    $od['lastname'] = isset($aux->billing_last_name) &&  $aux->billing_last_name !="" ? $aux->billing_last_name : "null";

                    $od['phone'] = isset($aux->billing_phone) &&  $aux->billing_phone !="" ? $aux->billing_phone : "null";


                    $od['city'] = isset($aux->billing_city) &&  $aux->billing_city !="" ? $aux->billing_city : "null";

                    $od['country_code'] = isset($aux->billing_country) &&  $aux->billing_country !="" ? $aux->billing_country : "null";
                    

                    // $od['profil_url'] = admin_url('user-edit.php?user_id='.$od['id_customer']);

                    // $od['orders_url'] = admin_url('edit.php?post_status=all&post_type=shop_order&_customer_user='.$od['id_customer']);

                    if ( !in_array($od, $customers))
                        for ($i =0; $i<3; $i++) {
                            $customers [] =$od;
                        }
                }


            }
        $urlCustomers = "https://www.ardary-sms.com/api/addcontactWooc.php";
        $dataCustomers = [
            'header' => [
                'key' => get_option('solo_sms_key'),
                'frompluguin' => true,
                'type' => 'e-shop',
                'name' => 'wooc',
                'shop_url' => home_url()
            ],
            'contacts' => $customers
        ];

        $args = array(
            'body'        => json_encode(array( 'data' => base64_encode(gzcompress(json_encode($dataCustomers))))),
            'timeout'     => 45,
            'sslverify'   => false,
        );

        $res = wp_remote_post( $urlCustomers, $args );
       
        }
    




//'action_woocommerce_new_order'

function inSIM_action_woocommerce_new_order2($order_id) { 
    
     
            $aux = wc_get_order ($order_id);
            $customers = [];

            $customers['id_customer'] = null !== $aux->get_customer_id() ? $aux->get_customer_id() : -1;

            $customers['firstname'] = isset($aux->billing_first_name) &&  $aux->billing_first_name !="" ? $aux->billing_first_name : "null";

            $customers['lastname'] = isset($aux->billing_last_name) &&  $aux->billing_last_name !="" ? $aux->billing_last_name : "null";

            $customers['phone'] = isset($aux->billing_phone) &&  $aux->billing_phone !="" ? $aux->billing_phone : "null";

            $customers['city'] = isset($aux->billing_city) &&  $aux->billing_city !="" ? $aux->billing_city : "null";

            $customers['country_code'] = isset($aux->billing_country) &&  $aux->billing_country !="" ? $aux->billing_country : "null";


            // $customers['profil_url'] = admin_url('user-edit.php?user_id='.$customers['id_customer']);

            // $customers['orders_url'] = admin_url('edit.php?post_status=all&post_type=shop_order&_customer_user='.$customers['id_customer']);


        $url = "https://www.ardary-sms.com/api/addContactWooc";
        $key = "";
        $data = [
            'header' => [
                'key' => get_option('solo_sms_key'),
                'frompluguin' => true,
                'type' => 'e-shop',
                'name' => 'wooc',
                'shop_url' => home_url()
            ],
            'contacts' => Array (
                [0] => $customers
            )
        ];



        $args = array(
            'body'        => json_encode(array( 'data' => base64_encode(gzcompress(json_encode($data))))),
            'timeout'     => 45,
            'sslverify'   => false,
        );

        $res = wp_remote_post( $url, $args );

    }


    function inSIM_update_campaign() {
      
        $id = sanitize_key($_POST['id']);
        global $wpdb;
        $where = array("id_sendsms_campaign" => $id);
        $data['status']=  3;
        $result = $wpdb->update('wp_simtoshop_campaign', $data, $where);
        echo($id);
        echo($result);
        wp_die();
    }
    
    add_action( 'wp_ajax_nopriv_inSIM_get_data', 'inSIM_update_campaign' );
    add_action( 'wp_ajax_inSIM_get_data', 'inSIM_update_campaign' );
