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
if (!defined('ABSPATH'))
    exit; 

if (!class_exists('Sim_To_Shop_Send_Tab')) {

    include_once(plugin_dir_path(__FILE__) . 'sim-to-shop-generic-campaign-tab.php');
   

    class Sim_To_Shop_Send_Tab extends Sim_To_Shop_Generic_Campaign_Tab {

        protected $_status = array(0, 1, 2);

        protected function _get_display_status() {
            return $this->_status;
        }

        protected $post;

        protected function _post_process() {
            global $wpdb;
            if (!isset($_REQUEST)) {
                return;
            }
            $this->post = $_REQUEST; //sanitize_post($_REQUEST, 'edit');

            if (isset($this->post['sendsms_save2']) && $this->_campaign->status == 0) {
                echo'<script>function nextStep(step){
                    document.getElementById("defaultOpen"+step).click();
                }
                
                window.addEventListener("DOMContentLoaded", () => {nextStep(4);});</script>';
            }

            if (isset($this->post['sendsms_save']) && $this->_campaign->status == 0) {
                //create campaign
                $this->_post_validation();
                if (!sizeof(self::get_errors())) {
                    $this->_campaign->ticket = (string) time();
                    $this->_campaign->title = sanitize_text_field($this->post['sendsms_title']);
                    $this->_campaign->message = $this->post['sendsms_message'];
                    $date = strtotime($this->post['sendsms_date'] . ' ' . (int) (isset($this->post['sendsms_date_hour']) ? $this->post['sendsms_date_hour'] : 0) . ':' . (isset($this->post['sendsms_date_minute']) ? $this->post['sendsms_date_minute'] : 0) . ':00');
                    $this->_campaign->date_send = date_i18n('Y-m-d H:i:s', $date);
                    $this->_campaign->isteam = sanitize_text_field($this->post['isteam']);
                    $this->_campaign->save();
                    self::add_message(__('Your campaign has been saved.', 'insim'));
                    echo'<script>function nextStep(step){
                        document.getElementById("defaultOpen"+step).click();
                    }
                    
                    window.addEventListener("DOMContentLoaded", () => {nextStep(4);});</script>';
                }
            } else if ($this->_campaign->status == 0 && isset($_FILES['sendsms_csv']['tmp_name']) && !empty($_FILES['sendsms_csv']['tmp_name'])) {
                //import a csv file and create campaign if it not exists
                if (!$this->post['id_sendsms_campaign']) {
                    $this->_campaign->ticket = (string) time();
                    $this->_campaign->title = isset($this->post['sendsms_title']) && $this->post['sendsms_title'] != '' ? sanitize_text_field($this->post['sendsms_title']) : 'CAMPAIGN-' . $this->_campaign->ticket;
                    $this->_campaign->message = sanitize_text_field($this->post['sendsms_message']);
                    $date = strtotime($this->post['sendsms_date'] . ' ' . (int) (isset($this->post['sendsms_date_hour']) ? $this->post['sendsms_date_hour'] : 0) . ':' . (isset($this->post['sendsms_date_minute']) ? $this->post['sendsms_date_minute'] : 0) . ':00');
                    $this->_campaign->date_send = date_i18n('Y-m-d H:i:s', $date);
                    $this->_campaign->isteam = sanitize_text_field($this->post['isteam']);
                    $this->_campaign->save();
                }
                $tempFile = $_FILES['sendsms_csv']['tmp_name'];
                if (!is_uploaded_file($tempFile))
                    self::add_error(__('The file has not been uploaded', 'insim'));
                else {
                    $cpt = 0;
                    $line = 0;
                    if (($fd = fopen($tempFile, "r")) !== FALSE) {
                        while (($data = fgetcsv($fd, 1000, ";")) !== FALSE) {
                            $line++;
                            if (count($data) >= 1) {
                                $phone = $data[0];
                                // If not international phone
                                if (substr($phone, 0, 1) != '+')
                                    continue;
                                $firstname = isset($data[1]) ? $data[1] : null;
                                $lastname = isset($data[2]) ? $data[2] : null;
                                // if phone is not valid
                                if (!WC_Validation::is_phone($phone))
                                    continue;
                                $recipient = new Sim_To_Shop_Recipient();
                                $recipient->id_sendsms_campaign = $this->_campaign->id_sendsms_campaign;
                                $recipient->firstname = $firstname;
                                $recipient->lastname = $lastname;
                                $recipient->phone = $phone;
                                $recipient->status = 0;
                                // can fail if that phone number already exist for that campaign
                                try {
                                    $nbr = $recipient->save();
                                    if ($nbr)
                                        $cpt++;
                                } catch (Exception $e) {
                                    
                                }
                            }
                        }
                        fclose($fd);
                    }
                    if ($line == 0)
                        self::add_error(__('That file is not a valid CSV file.', 'insim'));
                    else {
                        $this->_campaign->compute_campaign();
                        self::add_message($cpt . ' ' . __('new recipient(s) were added to the list.', 'insim') . ($line - $cpt > 0 ? ' ' . ($line - $cpt) . ' ' . __('line(s) ignored.', 'insim') : ''));
                    }
                }
            } else if (isset($this->post['sendsms_transmit']) && $this->_campaign->status <= 1) {
               
                if ($this->_campaign->status == 0) {
                    $this->_post_validation();
                    if (!sizeof(self::get_errors())) {
                        $this->_campaign->title = $this->post['sendsms_title'];
                        $this->_campaign->message = $this->post['sendsms_message'];
                        $this->_campaign->isteam = sanitize_text_field($this->post['isteam']);

                        $count = $wpdb->get_var("
                            SELECT count(*)
                            FROM `" . $wpdb->prefix . "simtoshop_recipient` 
                            WHERE id_sendsms_campaign=" . $this->_campaign->id_sendsms_campaign . "
                            AND phone like '+33%'
                        "); 
                        if ($count > 0 && strpos($this->_campaign->message, _STR_STOP_) == true) {
                            self::add_error(Sim_To_Shop_Admin::get_instance()->get_error_SMS(_ERROR_STOP_MENTION_IS_MISSING_));
                        } else {
                            $this->_campaign->date_transmitted = current_time('mysql');
                            $date = strtotime($this->post['sendsms_date'] . ' ' . (int) (isset($this->post['sendsms_date_hour']) ? $this->post['sendsms_date_hour'] : 0) . ':' . (isset($this->post['sendsms_date_minute']) ? $this->post['sendsms_date_minute'] : 0) . ':00');
                            $this->_campaign->date_send = current_time('mysql');
                            if(strtotime($this->_campaign->date_send) < strtotime($this->_campaign->date_transmitted)){
                                 $this->_campaign->date_send = current_time('mysql');
                            }
                            $this->_campaign->status = 1;
                        }
                        $this->_campaign->save();
                    }
                } else
                    self::add_message(__('Your campaign is currently being transmitted, please do not close the window.', 'insim'));
            } else if (isset($this->post['sendsms_validate']) && $this->_campaign->status == 2) {
                $this->_post_validation();
                if (!sizeof(self::get_errors())) {
                    $this->_campaign->title = sanitize_text_field($this->post['sendsms_title']);
                    $this->isteam = sanitize_text_field($this->post['isteam']);
                    $date = strtotime($this->post['sendsms_date'] . ' ' . (int) (isset($this->post['sendsms_date_hour']) ? $this->post['sendsms_date_hour'] : 0) . ':' . (isset($this->post['sendsms_date_minute']) ? $this->post['sendsms_date_minute'] : 0) . ':00');
                    $this->_campaign->date_send = date_i18n('Y-m-d H:i:s', $date);
                    $this->_campaign->save();

               
                    $xml = Sim_To_Shop_API::get_instance()->validate_campaign($this->_campaign->ticket, $this->_campaign->date_send); 
                    $xml = simplexml_load_string($xml);
                    if ($xml->error_code == '000') {
                        $this->_campaign->status = 3;
                        $this->_campaign->date_validation = current_time('mysql');
                        $this->_campaign->date_send = current_time('mysql');
                        if(strtotime($this->_campaign->date_send) < strtotime($this->_campaign->date_validation)){
                            $this->_campaign->date_send = current_time('mysql');
                        }
                        self::add_message(__('Your campaign is now validated and will be sent at', 'insim') . ' ' . $this->_campaign->date_send);
                        $this->_campaign->save();
                    } else {
                        self::add_error(Sim_To_Shop_Admin::get_instance()->get_error_SMS($xml->error_code));
                    }
                } else {
                    self::add_error(Sim_To_Shop_Admin::get_instance()->get_error_SMS($xml->error_code));
                }
            } else if (isset($this->post['sendsms_cancel']) && $this->_campaign->status >= 1 && $this->_campaign->status < 3 && !($this->_campaign->status == 3 && current_time('mysql') > $this->_campaign->date_send)) {
                if ($this->_campaign->nb_recipients > 0) {
                    $xml = Sim_To_Shop_API::get_instance()->cancel_campaign($this->_campaign->ticket);
                    $xml = simplexml_load_string($xml);
                    if ($xml->error_code == '000' || intval($xml->error_code) == _ERROR_BATCH_SMS_NOT_FOUND_) {
                        $this->_campaign->status = 4;
                        $this->_campaign->save();
                        self::add_message(__('Your campaign is now cancelled on solo and can be deleted', 'insim', 'insim'));
                    } else {

                        self::add_error(Sim_To_Shop_Admin::get_instance()->get_error_SMS($xml->error_code));
                    }
                } else {
                    $this->_campaign->status = 4;
                    $this->_campaign->save();
                    self::add_message(__('Your campaign is now cancelled and can be deleted', 'insim'));
                }
            } else if (isset($this->post['sendsms_duplicate']) && $this->_campaign->id_sendsms_campaign) {
                $old_id = $this->_campaign->id_sendsms_campaign;
                $this->_campaign->id_sendsms_campaign = null;
                $this->_campaign->status = 0;
                $this->_campaign->nb_recipients = 0;
                $this->_campaign->nb_sms = 0;
                $this->_campaign->price = 0;
                $this->_campaign->ticket = (string) time();
                $this->_campaign->date_transmitted = NULL;
                $this->_campaign->date_validation = NULL;
                $this->_campaign->date_add = date('Y-m-d H:i:s');
                $this->_campaign->date_upd = date('Y-m-d H:i:s');
                $this->_campaign->save();
                if (WP_DEBUG) error_log ("Your campaign has been duplicated ".$this->_campaign->id_sendsms_campaign);

                //duplicate the recipients
                $wpdb->query('
                    INSERT INTO `' . $wpdb->prefix . 'simtoshop_recipient` (`id_sendsms_campaign`, `id_customer`, `firstname`, `lastname`, `phone`, `iso_country`, `transmitted`, `price`, `nb_sms`, `status`, `ticket`, `date_add`, `date_upd`)
                    SELECT ' . $this->_campaign->id_sendsms_campaign . ', `id_customer`, `firstname`, `lastname`, `phone`, `iso_country`, 0, 0, 0, 0, NULL, NOW(), NOW() FROM `' . $wpdb->prefix . 'simtoshop_recipient` WHERE `id_sendsms_campaign`=' . $old_id);
                $nb_recipients = $wpdb->get_var('SELECT count(*) AS total FROM `' . $wpdb->prefix . 'simtoshop_recipient` WHERE `id_sendsms_campaign`=' . $this->_campaign->id_sendsms_campaign);
                $this->_campaign->nb_recipients = $nb_recipients;
                $this->_campaign->save();
                
                self::add_message(__('Your campaign has been duplicated, you are now working on a new campaign.', 'insim'));
            } else if (isset($this->post['sendsms_delete']) && ($this->_campaign->status == 0 || $this->_campaign->status >= 3)) {
                //delete the campaign if it is possible
                $res = $this->_campaign->delete();
                if ($res == false) {
                    self::add_error(__('Your campaign can not be deleted.', 'insim'));
                } else {
                    $this->_campaign->id_sendsms_campaign = 0;
                    self::add_message(__('Your campaign has been deleted.', 'insim'));
                }
            }
        }

        private function _post_validation() {
            if (isset($this->post['sendsms_save']) || isset($this->post['sendsms_duplicate']) || isset($this->post['sendsms_transmit'])) {
                if (!$this->post['sendsms_title'])
                    self::add_error(__('Please enter a title', 'insim'));
                if (!$this->post['sendsms_message'])
                    self::add_error(__('Please enter a message', 'insim'));
                if (!$this->post['sendsms_date'])
                    self::add_error(__('Please enter a valid send date', 'insim'));
                else {
                    // Update date
                }
            }
        }

        public static function _ajax_process_transmitOWS() {
            global $wpdb;
            $post = $_REQUEST;

            if ($post['id_sendsms_campaign']) {
                if (WP_DEBUG)
                    error_log('Ajax transmitOWS ' . print_r($post, true));
                $campaign = new Sim_To_Shop_Campaign($post['id_sendsms_campaign']);

                //send 200 by 200 recipients
                $result = $wpdb->get_results('
                    SELECT SQL_CALC_FOUND_ROWS *
                    FROM `' . $wpdb->prefix . 'simtoshop_recipient`
                    WHERE id_sendsms_campaign=' . $campaign->id_sendsms_campaign . '
                    AND transmitted = 0
                    ORDER BY id_sendsms_recipient ASC
                LIMIT 200'); 
                $size = count($result);
                $total_rows = $wpdb->get_var('SELECT FOUND_ROWS()');
                if (WP_DEBUG)
                    error_log('Ajax transmitOWS ' . $size . " " . $total_rows);
                $finished = false;
                $campaign_can_be_send = false;
                if ((int) $size == (int) $total_rows)
                    $finished = true;

                $error = false;
                $message = false;
                //if there are other recipients to add
                if ($size != 0) {
                    //send recipient
                    $recipients = array();
                    foreach ($result as $recipient) {
                        $recipients[$recipient->phone] = $recipient;
                    }
                    // call OWS and get XML result                    
                    $xml = Sim_To_Shop_API::get_instance()->send_trame($campaign->ticket, $recipients, $campaign->message, $finished);
                    $xml = simplexml_load_string($xml);
                    if (WP_DEBUG)
                        error_log('Ajax transmitOWS xml result' . print_r($xml, true));

                    if ($xml->error_code == '000') {
                        // success
                        foreach ($xml->successs->success as $success) {
                            $phone = (string) $success->recipient;
                            $recipients[$phone]->price = $success->cost;
                            //TODO $recipients[$phone]->nb_sms = $success->sms_needed;
                            $recipients[$phone]->status = 0;
                            $recipients[$phone]->ticket = (string) $xml->ticket;
                            $recipients[$phone]->transmitted = 1;
                        }

                        // errors
                        foreach ($xml->failures->failure as $failure) {
                            $phone = (string) $failure->recipient;
                            $recipients[$phone]->price = 0;
                            //TODO $recipients[$phone]->nb_sms = 0;
                            $recipients[$phone]->status = $failure->error_code;
                            $recipients[$phone]->ticket = (string) $xml->ticket;
                            $recipients[$phone]->transmitted = 1;
                        }

                        // convert recipient to Sim_To_Shop_Recipient
                        foreach ($recipients as $key => $recipient) {
                            // update th recipient information
                            $obj = Sim_To_Shop_Recipient::get_recipient($campaign->id_sendsms_campaign, $key);
                            foreach ($recipient as $field => $value)
                                $obj->$field = $value;
                            $obj->save();
                        }

                        // update the campaign totals
                        $campaign->date_transmitted = current_time('mysql');
                        $campaign->date_send = current_time('mysql');
                        $campaign->compute_campaign(1);  
                        $campaign->status_label = Sim_To_Shop_Admin::get_instance()->get_status($campaign->status);                        
                    } else {
                        $error = Sim_To_Shop_Admin::get_instance()->get_error_SMS($xml->error_code);
                    }
                }


                if (!$error && $finished) {
                    $xml = Sim_To_Shop_API::get_instance()->get_campaign_status($campaign->ticket);
                    $xml = simplexml_load_string($xml);
                    if ($xml->error_code == '000') {
                        $campaign->status = 2;
                        $campaign->status_label = Sim_To_Shop_Admin::get_instance()->get_status($campaign->status);
                        $campaign->price = floatval($xml->cost);
                        $campaign->save();
                        $campaign_can_be_send = true;
                    } else {
                        $message = Sim_To_Shop_Admin::get_instance()->get_error_SMS($xml->error_code);
                    }
                }
                wp_send_json(array('campaign' => $campaign, 'finished' => $campaign_can_be_send, 'total_rows' => $total_rows - $size, 'error' => $error, 'message' => $message));
            }
        }

        public static function _ajax_process_delRecipient() {
            if (WP_DEBUG)
                error_log("Request:" . print_r($_REQUEST, true));
            //$post = $_REQUEST;

            $campaign = new Sim_To_Shop_Campaign(sanitize_key($_REQUEST['id_sendsms_campaign']));
            if ($campaign->status == 0) {
                $recipient = new Sim_To_Shop_Recipient(sanitize_key($_REQUEST['id_sendsms_recipient']));
                $recipient->delete();
                $campaign->compute_campaign();
            }
            wp_send_json(array('campaign' => $campaign, 'valid' => true));
        }

        /**
         * Filter customer
         */
        public static function _ajax_process_filter() {
            global $wpdb;
            /**
             * Get users
             */
            $admin_users = new WP_User_Query(
                    array(
                'role' => 'administrator',
                'fields' => 'ID'
                    )
            );

            $manager_users = new WP_User_Query(
                    array(
                'role' => 'shop_manager',
                'fields' => 'ID'
                    )
            );
            $per_page = 1000000;
            $current_page = 1;
            $query = new WP_User_Query(array(
                'exclude' => array_merge($admin_users->get_results(), $manager_users->get_results()),
                'number' => $per_page,
                'offset' => ( $current_page - 1 ) * $per_page
            ));

            $s = !empty($_REQUEST['q']) ? stripslashes(sanitize_key($_REQUEST['q'])) : '';

            $query->query_from .= " LEFT JOIN {$wpdb->usermeta} as meta2 ON ({$wpdb->users}.ID = meta2.user_id) ";
            $query->query_orderby = " ORDER BY meta2.meta_value, user_login ASC ";
            $query->query_fields .= " meta2.meta_key meta2.meta_value";
            if ($s) {
                $query->query_where .= " AND ( user_id LIKE '%" . esc_sql(str_replace('*', '', $s)) . "%' OR user_login LIKE '%" . esc_sql(str_replace('*', '', $s)) . "%' OR user_nicename LIKE '%" . esc_sql(str_replace('*', '', $s)) . "%' OR ( meta2.meta_key = 'billing_phone' and meta2.meta_value LIKE '%" . esc_sql(str_replace('*', '', $s)) . "%')  ";
                $query->query_where .= " OR ( meta2.meta_key = 'billing_first_name' and meta2.meta_value LIKE '%" . esc_sql(str_replace('*', '', $s)) . "%') ";
                $query->query_where .= " OR ( meta2.meta_key = 'billing_last_name' and meta2.meta_value LIKE '%" . esc_sql(str_replace('*', '', $s)) . "%') ";
                $query->query_where .= " )";
                $query->query_orderby = " GROUP BY ID " . $query->query_orderby;
            }

            $request = "select * " . " " .$query->query_from . " ".$query->query_where." " . $query->query_orderby;
            
            if (WP_DEBUG)
                error_log("Customer request:" . $request);//.",".print_r($query,true));
            
            $customers = $wpdb->get_results($request);
            $res = array();
            $nbr_value_return = 30;
            foreach ($customers as $customerSql) {
                    if ($nbr_value_return < count($res)) {
                        break;
                    }
                    $res[] = array(
                    'label' => get_user_meta($customerSql->ID,'billing_last_name')[0] . " " . get_user_meta($customerSql->ID,'billing_first_name')[0] . " " . get_user_meta($customerSql->ID,'billing_phone')[0],
                    'obj' => array('id_customer' => $customerSql->ID, 'phone' => get_user_meta($customerSql->ID,'billing_phone')[0], 'firstname' => get_user_meta($customerSql->ID,'billing_first_name')[0],
                         'lastname' => get_user_meta($customerSql->ID,'billing_last_name')[0], 'iso_country' => '', 'country' => '')
                );
            }                
            echo $_REQUEST['callback'] . json_encode($res) ;
            wp_die();
        }

        public static function _ajax_process_countRecipientsFromQuery() {
            //$post = $_REQUEST;
            $campaign = new Sim_To_Shop_Campaign(sanitize_key($_REQUEST['id_sendsms_campaign']));
            $result = $campaign->get_recipients_from_query(true);
            wp_send_json(array('total_rows' => (int) $result));
        }

        public static function _ajax_process_addRecipientsFromQuery() {
            global $wpdb;
            //$post = $_REQUEST;
            //create campaign if the campaign does not exist
            if (!sanitize_key($_REQUEST['id_sendsms_campaign'])) {
                $campaign = new Sim_To_Shop_Campaign();
                $campaign->ticket = (string) time();
                $campaign->title = sanitize_key($_REQUEST['sendsms_title']) == '' ? 'CAMPAIGN-' . $campaign->ticket : sanitize_key($_REQUEST['sendsms_title']);
                $campaign->message = sanitize_key($_REQUEST['sendsms_message']);
                $campaign->date_send = sanitize_key($_REQUEST['sendsms_date_send']);
                $campaign->isteam = sanitize_key($post['isteam']);
                $campaign->save();
            } else
                $campaign = new Sim_To_Shop_Campaign(sanitize_key($_REQUEST['id_sendsms_campaign']));

            //get the recipients from the query
            $result = $campaign->get_recipients_from_query(false);
            $cpt = 0;
            //we add each recipient to the campaign
            if (is_array($result)) {
                $phone_prefix = array();
                //preload phone prefix
                $phones_prefix = $wpdb->get_results("SELECT iso_code, prefix FROM `" . $wpdb->prefix . "simtoshop_phone_prefix`");
                foreach ($phones_prefix as $prefix) {
                    $phone_prefix[$prefix->iso_code] = $prefix->prefix;
                }
                foreach ($result as $row) {
                    //print_r($row);
                    $recipient = new Sim_To_Shop_Recipient();
                    $recipient->id_sendsms_campaign = $campaign->id_sendsms_campaign;
                    $recipient->id_customer = (int) $row->ID;
                    $recipient->firstname = $row->billing_firstname;
                    $recipient->lastname = isset($row->billing_lastname) && $row->billing_lastname != '' ? $row->billing_lastname : $row->display_name;
                    $recipient->lastname = preg_replace('/[0-9]+/', '', $recipient->lastname);
                    $phone = Sim_To_Shop_API::convert_phone_to_international($row->billing_phone, $row->billing_country);
                    // if (is_null($phone))
                    //     continue;
                    $recipient->phone = $phone;
                    $recipient->iso_country = $row->billing_country;
                    $recipient->status = 0;
                    //print_r($recipient);die();
                    // can fail if that phone number already exist for that campaign
                    try {
                        if (!$recipient->save()) {
                            
                            error_log("Error when try to add:".print_r($recipient,true));
                        } else {
                            $cpt++;
                        }
                    } catch (Exception $e) {
                        error_log("Error when try to add:".print_r($e,true));
                    }
                }
                $campaign->compute_campaign();
            }
            wp_send_json(array('campaign' => $campaign, 'total_rows' => (int) $cpt));
        }

        public static function findOrCreateCampaign($post) {
            //create campaign if the campaign does not exist
            if (!sanitize_key($post['id_sendsms_campaign'])) {
                $campaign = new Sim_To_Shop_Campaign();
                $campaign->ticket = (string) time();
                $campaign->title = sanitize_key($post['sendsms_title']) == '' ? 'CAMPAIGN-' . $campaign->ticket : sanitize_key($post['sendsms_title']);
                $campaign->message = sanitize_key($post['sendsms_message']);
                $campaign->date_send = sanitize_key($post['sendsms_date_send']);
                $campaign->isteam = sanitize_text_field($this->post['isteam']);

                //$campaign->save();
            } else
                $campaign = new Sim_To_Shop_Campaign(sanitize_key($post['id_sendsms_campaign']));
            return $campaign;
        }

        public static function addRecipients($result) {
            $cpt = 0;
            //we add each recipient to the campaign
            if (is_array($result)) {
                $phone_prefix = array();
                //preload phone prefix
                $phones_prefix = $wpdb->get_results("SELECT iso_code, prefix FROM `" . $wpdb->prefix . "simtoshop_phone_prefix`");
                foreach ($phones_prefix as $prefix) {
                    $phone_prefix[$prefix->iso_code] = $prefix->prefix;
                }
                foreach ($result as $row) {
                    $recipient = new Sim_To_Shop_Recipient();
                    $recipient->id_sendsms_campaign = $campaign->id_sendsms_campaign;
                    $recipient->id_customer = (int) $row->ID;
                    $recipient->firstname = $row->billing_firstname;
                    $recipient->lastname = isset($row->billing_lastname) && $row->billing_lastname != '' ? $row->billing_lastname : $row->user_login;
                    $recipient->lastname = preg_replace('/[0-9]+/', '', $recipient->lastname);
                    $phone = Sim_To_Shop_API::get_instance()->convert_phone_to_international($row->billing_phone, $row->billing_country);
                    if (is_null($phone))
                        continue;
                    $recipient->phone = $phone;
                    $recipient->iso_country = $row->billing_country;
                    $recipient->status = 0;
                    // can fail if that phone number already exist for that campaign
                    try {
                        if (!$recipient->save()) {
                            error_log("Error when try to add:".print_r($recipient,true));
                        } else {
                            $cpt++;
                        }
                    } catch (Exception $e) {
                        error_log("Error when try to add:".print_r($e,true));
                    }
                }
                $campaign->compute_campaign();
            }
            return $cpt;
        }

        /**
         * Filter user
         */
         public static function _ajax_process_filter_user() {
            global $wpdb;
            //$post = $_REQUEST;

            $role = !empty(sanitize_key($_REQUEST['q'])) ? stripslashes(sanitize_key($_REQUEST['q'])) : '';
            $rolesafe = esc_sql(str_replace('*', '', $role));
            $id_sendsms_campaign=sanitize_key($_REQUEST['id_sendsms_campaign']);
            $id_sendsms_campaign=esc_sql(str_replace('*', '', $id_sendsms_campaign));            
            
            $campaign = Sim_To_Shop_Send_Tab::findOrCreateCampaign($_REQUEST);
            $userIds = $campaign->get_recipients_from_role($rolesafe);
            
            //2 roles exceptions (all and none)
            // $userIds = $query_users->results;
            // foreach ($userIds as $id) {
            //     error_log("phoneee".get_user_meta($id,'billing_phone')[0]);                     
            // }
            wp_send_json(array('total_rows' => (int) count($userIds)));
            wp_die();
        }

        public static function _ajax_process_addRecipientsFromRole() {
            global $wpdb;
            //$post = $_REQUEST;

            $role = !empty(sanitize_key($_REQUEST['q'])) ? stripslashes(sanitize_key($_REQUEST['q'])) : '';
            $rolesafe = esc_sql(str_replace('*', '', $role));
            $id_sendsms_campaign=sanitize_key($_REQUEST['id_sendsms_campaign']);
            $id_sendsms_campaign=esc_sql(str_replace('*', '', $id_sendsms_campaign));            
            
            $campaign = Sim_To_Shop_Send_Tab::findOrCreateCampaign($_REQUEST);
            $userIds = $campaign->get_recipients_from_role($rolesafe);
            $result = $campaign->addRecipientsFromUserIds($userIds);
            wp_send_json(array('campaign' => $campaign, 'total_rows' => (int) $userIds-$result['added'],'errors' => $result['errors']));
        }        

        /**
         * Add recipient to a campaign (call via ajax)
         */
        public static function _ajax_process_addRecipient() {
            $post = $_REQUEST;

            $phone = sanitize_key($post['phone']);
            // if phone is not valid
            if (!WC_Validation::is_phone($phone))
                wp_send_json(array('error' => __('That phone number is invalid.', 'insim')));
            // if we know the country, try to convert the phone to international
            if (sanitize_key($post['iso_country'])) {
                $phone = Sim_To_Shop_API::get_instance()->convert_phone_to_international($phone, sanitize_key($post['iso_country']));
                if (is_null($phone))
                    wp_send_json(array('error' => __('The phone number and the country does not match.', 'insim')));
            }
            if (!sanitize_key($post['id_sendsms_campaign'])) {
                $campaign = new Sim_To_Shop_Campaign();
                $campaign->ticket = (string) time();
                $campaign->title = sanitize_key($post['sendsms_title']) == '' ? 'CAMPAIGN-' . $campaign->ticket : sanitize_key($post['sendsms_title']);
                $campaign->message = sanitize_key($post['sendsms_message']);
                $date = strtotime(sanitize_key($post['sendsms_date']) . ' ' . (int) (isset($post['sendsms_date_hour']) ? sanitize_key($post['sendsms_date_hour']) : 0) . ':' . (isset($post['sendsms_date_minute']) ? sanitize_key($post['sendsms_date_minute']) : 0) . ':00');
                $campaign->date_send = date_i18n('Y-m-d H:i:s', $date);
                $campaign->isteam = sanitize_key($post['isteam']);
                $campaign->save();
            } else
                $campaign = new Sim_To_Shop_Campaign(sanitize_key($post['id_sendsms_campaign']));
            if (WP_DEBUG)
                error_log("Campaign" . print_r($campaign, true));

            $recipient = new Sim_To_Shop_Recipient();
            $recipient->id_sendsms_campaign = $campaign->id_sendsms_campaign;
            $recipient->id_customer = (int) sanitize_key($post['id_customer']);
            $recipient->firstname = isset($post['sendsms_firstname']) ? sanitize_key($post['sendsms_firstname']) : '';
            $recipient->lastname = isset($post['sendsms_lastname']) ? sanitize_key($post['sendsms_lastname']) : '';
            if ($recipient->firstname == '' && sanitize_key(isset($post['firstname'])))
                $recipient->firstname = sanitize_key($post['firstname']);
            if ($recipient->lastname == '' && sanitize_key(isset($post['lastname'])))
                $recipient->lastname = sanitize_key($post['lastname']);
            $recipient->phone = $phone;
            $recipient->iso_country = sanitize_key($post['iso_country']);
            $recipient->status = 0;
            // can fail if that phone number already exist for that campaign
            try {
                if (WP_DEBUG)
                    error_log("Save recipient: " . print_r($recipient, true));
                $res = $recipient->save();
                if (WP_DEBUG)
                    error_log("Saved recipient: " . print_r($res, true));
                if ($res) {
                    $campaign->compute_campaign();
                    wp_send_json(array('campaign' => $campaign, 'recipient' => $recipient));
                } else
                    wp_send_json(array('error' => __('That phone number is already in the list.', 'insim')));
            } catch (Exception $e) {
                wp_send_json(array('error' => __('That phone number is already in the list.', 'insim')));
            }
        }

    }

}