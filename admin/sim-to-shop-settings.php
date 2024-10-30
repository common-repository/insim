<?php
/*
 * Copyright (C) 2022 simtoshop
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
if (!defined('ABSPATH')) {
    exit; 
}

if (!class_exists('Sim_To_Shop_Settings')) :

    /**
     * Sim_To_Shop_Settings
     */
    class Sim_To_Shop_Settings extends WC_Settings_Page {

        public $balance;
        public $bAuth = false;

       
        public function __construct() {
            $this->id = 'insim';
            $this->label = __('Send SMS', 'insim');            
        }


        public function output() {
            $this->_post_process();
            echo WC_Admin_Settings::show_messages();
            include_once( 'partials/html-sim-to-shop-settings.php' );
        }

   
        private function _post_process() {
            if (array_key_exists('solo_sms_email', $_POST)) {
                sanitize_post($_POST);
                $email = wc_clean($_POST['solo_sms_email']);
                if (array_key_exists('solo_sms_email', $_POST))
                    $key = wc_clean($_POST['solo_sms_key']);
                if (array_key_exists('solo_sms_sender', $_POST))
                    $sender = wc_clean($_POST['solo_sms_sender']);

                if (array_key_exists('solo_sms_admin_phone', $_POST))
                    $admin_phone = wc_clean($_POST['solo_sms_admin_phone']);
                if (array_key_exists('solo_sms_admin_alert', $_POST))
                    $admin_alert = wc_clean($_POST['solo_sms_admin_alert']);
                $freeoption = null;
                $product_id = 0;
                error_log(print_r($_POST, true));
                if (array_key_exists('solo_sms_freeoption', $_POST)) {
                    if ($_POST['solo_sms_freeoption'] == (int) 1) {
                        $freeoption = 1;
                    } else {
                        $freeoption = 0;
                        $product_id = wc_clean($_POST['solo_sms_option_id_product']);
                    }
                } else {
                    $freeoption = 0;
                    $product_id = wc_clean($_POST['solo_sms_option_id_product']);
                }

                if (empty($email) || empty($key)) {
                    WC_Admin_Settings::add_error(__('Please enter your account information to login to https://insim.app/', 'insim'));
                }
                if (!is_email($email)) {
                    WC_Admin_Settings::add_error(__('The email you entered is not a valid email.', 'insim'));
                } else {
                    update_option('solo_sms_email', $email);
                }
                if (!empty($key)) {
                    update_option('solo_sms_key', $key);
                   
                }

            }
            $this->balance = Sim_To_Shop_API::get_instance()->get_balance();
            $this->bAuth = $this->balance === false || $this->balance === '001' ? false : true;
        }

    }

    endif;

return new Sim_To_Shop_Settings();

