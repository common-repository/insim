<?php
/*
 * Copyright (C) 2014 sim_to_shop
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

if (!class_exists('Sim_To_Shop_Generic_Campaign_Tab')) {

    include_once 'models/sim-to-shop-campaign-model.php';
    include_once 'models/sim-to-shop-campaign.php';
    include_once 'models/sim-to-shop-recipient.php';
    include_once 'sim-to-shop-campaign-list.php';
    include_once 'sim-to-shop-recipient-list.php';

    abstract class Sim_To_Shop_Generic_Campaign_Tab {

        protected $_campaign;
        //protected $_errors;
        private static $errors = array();
        private static $messages = array();

        /**
         * Add a message
         * @param string $text
         */
        public static function add_message($text) {
            self::$messages[] = $text;
        }

        /**
         * Add an error
         * @param string $text
         */
        public static function add_error($text) {
            self::$errors[] = $text;
        }

        public static function get_errors() {
            return self::$errors;
        }

        /**
         * Output messages + errors
         * @return string
         */
        public static function show_messages() {
            if (sizeof(self::$errors) > 0) {
                foreach (self::$errors as $error) {
                    echo '<div id="message" class="error"><p><strong>' . esc_html($error) . '</strong></p></div>';
                }
            } elseif (sizeof(self::$messages) > 0) {
                foreach (self::$messages as $message) {
                    echo '<div id="message" class="updated"><p><strong>' . esc_html($message) . '</strong></p></div>';
                }
            }
        }

        public function get_body() {
            wp_enqueue_script('sim_to_shop_settings', WC()->plugin_url() . '/assets/js/admin/settings.min.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-autocomplete', 'jquery-ui-sortable', 'iris', 'chosen'), WC()->version, true);

            if (WP_DEBUG)
                error_log('Sim_To_Shop_Generic_Campaign_Tab - get_body() - $_REQUEST' . print_r($_REQUEST, true));
            //if post data, process the data

            $id_sendsms_campaign = (isset($_REQUEST['id_sendsms_campaign']) ? sanitize_key($_REQUEST['id_sendsms_campaign']) : null);
            $this->_campaign = new Sim_To_Shop_Campaign($id_sendsms_campaign);

            $this->_post_process();

            // Add any posted messages
            if (!empty($_GET['wc_error'])) {
                self::add_error(stripslashes($_GET['wc_error']));
            }

            if (!empty($_GET['wc_message'])) {
                self::add_message(stripslashes($_GET['wc_message']));
            }

            self::show_messages();

            $html = '
		<div id="' . get_class($this) . '">';
            if (($this->_campaign->id_sendsms_campaign || isset($_REQUEST['newCampaign']) || sizeof(self::$errors)) && in_array($this->_campaign->status, $this->_status)) {
                ob_start();
                $this->output();
                $html .= ob_get_clean();
                //$html .= $this->get_body_one_campaign();
            } else {
                $html .= $this->get_body_campaigns();
            }
            $html .= '</div>';

            return $html;
        }

        abstract protected function _post_process();

        abstract protected function _get_display_status();

        public static function get_form_url() {
            $uri = $_SERVER['REQUEST_URI'];
            $pos = strpos($_SERVER['REQUEST_URI'], '&action=');
            if ($pos !== false)
                $uri = substr($_SERVER['REQUEST_URI'], 0, $pos);
            return esc_url_raw($uri);
        }

        protected function get_body_campaigns() {
            $html ='';
            $html .=   Sim_To_Shop_Campaign_Model::count_campaigns([0, 1, 2, 3, 4],'') >0  && !get_option('isConnectedToInsim') ? '<style>
            .notice-success, div.updated {
                border-left-color: #f2c471;
            }
            </style>
            
            <div style= "margin-top: -35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 999px;
            width: 95%;background-color: orange; margin-bottom: 60px;" id="message" class="updated"><div style ="display: flex;
            justify-content: start;
            align-items: baseline; margin-left: 20px;"><h2 style="margin-right: 5px;"><strong>' . __('Warning :
            ','insim') . '</strong></h2>
            <b> Please connect your inSIM apk to your shop to send sms campaign by clicking <a href="'.admin_url('admin.php?page=insim&tab=settings').'">here</a>.</br></b></div> </div>' :'' ;
                
            $html .= '<a id ="newcmp" style ="background: #2271b1!important; color: #f6f7f7!important; font-size: 15px!important; padding: 11px 20px!important;" class="add-new-h2" href="' . $this->get_form_url() . '&action=Sim_To_Shop_Send_Tab&newCampaign=1">' . __('<img src="/wp_services/wp-content/plugins/insim/admin/partials/../img/plus-icon.svg" style="margin-right: 5px;"> New campaign', 'insim') . '</a></h2>';
   
            if (get_class($this) == 'Sim_To_Shop_Send_Tab') {
                $myListTable = new Sim_To_Shop_Campaign_List(array("status" => $this->_get_display_status()));
                $html.='<div style ="margin-top: 45px;" class="wrap">';
                $html .='<h2>' . __('Campaigns history', 'insim').'</h2>';
    
                $myListTable->prepare_items();
                
                ob_start();
                echo '<form style = "margin-top: -35px;" method="post">
                    <input type="hidden" name="page" value="my_list_campaign" />';
                $myListTable->search_box(__('Search campaign', 'insim'), 'search_id');
                echo '</form>';
                $myListTable->display();
                $html .= ob_get_clean();
                $html.='</div>';
            } else { 
                //$html .='<h2>' . __('Campaigns history', 'insim');
                $myListTable = new Sim_To_Shop_Campaign_List(array("status" => $this->_get_display_status()));
                $html.='<div style ="margin-top: 75px;" class="wrap">';
                $html .='<h2>' . __('Campaign history', 'insim').'</h2>';
    
                $myListTable->prepare_items();
                ob_start();
                echo '<form style = "margin-top: -45px;" method="post">
                    <input type="hidden" name="page" value="my_list_campaign" />';
                $myListTable->search_box(__('Search campaign', 'insim'), 'search_id');
                echo '</form>';
                $myListTable->display();
                $html .= ob_get_clean();
                $html.='</div>';
            }                    


            // $myListTable = new Sim_To_Shop_Campaign_List(array("status" => $this->_get_display_status()));
            // $html.='<div style ="margin-top: 75px;" class="wrap">';
            // $html .='<h2>' . __('Campaign history', 'insim').'</h2>';

            // $myListTable->prepare_items();
            // ob_start();
            // echo '<form style = "margin-top: -45px;" method="post">
            //     <input type="hidden" name="page" value="my_list_campaign" />';
            // $myListTable->search_box(__('Search campaign', 'insim'), 'search_id');
            // echo '</form>';
            // $myListTable->display();
            // $html .= ob_get_clean();
            // $html.='</div>';

            return $html;
        }

        /**
         * Output the metabox
         */
        public function output() {
            //self::init_address_fields();
            //get the balance
            $b_auth = get_option('solo_sms_email') ? true : false;
            ?>

            <style type="text/css">
                #post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
                .postbox  {
                    background: none!important; 
                    min-width: 0!important; 
                    border: none!important;
                }
            </style>

            <div class="postbox-container">
                <div   style ='padding-top: 20%; margin-right: 10px;' id="sendsms_message" class="postbox doc">
                       
                    <!-- <div style ="height: 100px; width : 450%; margin-top: 10px; background: white;"><span style="border-style :solid; margin-left: 5%;">1-Recipients</span><span style="border-style :solid; margin-left: 20%;">2-Message</span><span style="border-style :solid; margin-left: 20%;">3-Settings</span><span style="border-style :solid; margin-left: 20%;">4-Configuration</span></diV> -->
                    <?php if (get_class($this) == 'Sim_To_Shop_Send_Tab') { ?>
                        
                    <?php } else { ?>
                        <br><br>
                    <?php } ?>
                </div>
            </div>
            <style>
        /* Style the tab buttons */
.tablink {
    margin-top: 20px;
  background-color: #555;
  color: white;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  font-size: 17px;
  width: 24%;
  /* margin-left: 20px; */
}

/* Change background color of buttons on hover */
.tablink:hover {
  background-color: #777;
}

/* Set default styles for tab content */
.tabcontent {
  color: white;
  display: none;
  padding: 50px;
  text-align: center;
}

/* Style each tab content individually */
#London {background-color:red;}
#Paris {background-color:green;}
#Tokyo {background-color:blue;}
#Oslo {background-color:orange;}

</Style>
<button style ="border-radius: 15px 0 0 0; background-color: #777" class="tablink" onclick=" stp=1;document.getElementById('next_step').style.display = 'block'; document.getElementById('defaultOpen2').style.backgroundColor= '#555';document.getElementById('defaultOpen1').style.backgroundColor= '#777'; 
document.getElementById('defaultOpen3').style.backgroundColor= '#555';
document.getElementById('defaultOpen4').style.backgroundColor= '#555';
document.getElementById('postbox-container-2').style.display = 'none';
document.getElementById('sendsms_choose_recipient').style.display = 'block';
document.getElementById('sendsms_recipient').style.display = 'block';
document.getElementById('sendsms_msg').style.display = 'none';
document.getElementById('sendsms_titleA').style.display = 'none';
document.getElementById('sendsms_titleB').style.display = 'none';
document.getElementById('url_track').style.display = 'none';
document.getElementById('sendsms_msg2').style.display = 'none';
document.getElementById('meesageLength').style.display = 'none';
document.getElementById('sendsms_titleB').style.display = 'none';
document.getElementById('cmp_details').style.display = 'none';
document.getElementById('sendsms_buttons').style.display = 'none';




 " id="defaultOpen1"><div style ="font-size:20px;color:#765f5f;text-align:center;line-height:0; 0;border-radius:50%;background:white; margin-right: 10px; padding: 7px 14px; display: inline;">1</div>Recipients</button>

<button class="tablink" onclick=" stp=2; document.getElementById('next_step').style.display = 'block';document.getElementById('defaultOpen2').style.backgroundColor= '#777'; 
document.getElementById('defaultOpen1').style.backgroundColor= '#555'; 
document.getElementById('defaultOpen3').style.backgroundColor= '#555'; 
document.getElementById('defaultOpen4').style.backgroundColor= '#555'; 
document.getElementById('postbox-container-2').style.display = 'block'; document.getElementById('sendsms_choose_recipient').style.display = 'none';
document.getElementById('sendsms_recipient').style.display = 'none';
document.getElementById('sendsms_titleA').style.display = 'none';
document.getElementById('sendsms_titleB').style.display = 'none';
document.getElementById('sendsms_msg').style.display = 'block';
document.getElementById('sendsms_msg2').style.display = 'block';

document.getElementById('url_track').style.display = 'block';
document.getElementById('sendsms_choose_recipient').style.display = 'none';
document.getElementById('meesageLength').style.display = 'block';
document.getElementById('sendsms_titleB').style.display = 'none';
document.getElementById('cmp_details').style.display = 'none';
document.getElementById('sendsms_buttons').style.display = 'none';




jQuery('#url_tracking').click(function (e) {
                e.preventDefault();
                document.querySelector('[name = \'sendsms_message\']').value +=  '#'+document.getElementById('url_tracking_value').value+'#';
                document.getElementById('meesageLength').innerHTML = document.querySelector('[name = \'sendsms_message\']').value.length+`/160`;
                //[Lien_Tracking];
            });


            document.getElementById('meesageLength').innerHTML = document.querySelector('[name = \'sendsms_message\']').value.length+`/160`;
            document.querySelector('[name = \'sendsms_message\']').addEventListener('keyup', () => {
                document.getElementById('meesageLength').innerHTML = document.querySelector('[name = \'sendsms_message\']').value.length+`/160`;
            });

"
id="defaultOpen2"><div style ="font-size:20px;color:#765f5f;text-align:center;line-height:0; 0;border-radius:50%;background:white; margin-right: 10px; padding: 7px 14px; display: inline;">2</div>Message</button>

<button id="defaultOpen3" class="tablink" onclick="
document.getElementById('next_step').style.display = 'block';
stp=3;
document.getElementById('defaultOpen1').style.backgroundColor= '#555';
document.getElementById('defaultOpen2').style.backgroundColor= '#555';
document.getElementById('defaultOpen4').style.backgroundColor= '#555';
document.getElementById('defaultOpen3').style.backgroundColor= '#777';
document.getElementById('postbox-container-2').style.display = 'block';
document.getElementById('sendsms_recipient').style.display = 'none';
document.getElementById('sendsms_titleA').style.display = 'block';
document.getElementById('sendsms_titleB').style.display = 'block';

document.getElementById('sendsms_msg').style.display = 'none';
document.getElementById('sendsms_choose_recipient').style.display = 'none';
document.getElementById('url_track').style.display = 'none';
document.getElementById('sendsms_msg2').style.display = 'none';
document.getElementById('meesageLength').style.display = 'none';
document.getElementById('cmp_details').style.display = 'none';
document.getElementById('sendsms_buttons').style.display = 'none';



"><div style ="font-size:20px;color:#765f5f;text-align:center;line-height:0; 0;border-radius:50%;background:white; margin-right: 10px; padding: 7px 14px; display: inline;">3</div>Settings</button>

<button style ="border-radius: 0 0 15px 0;" id="defaultOpen4" class="tablink" onclick="
stp=4;
document.getElementById('next_step').style.display = 'none';
document.getElementById('defaultOpen1').style.backgroundColor= '#555';
document.getElementById('defaultOpen2').style.backgroundColor= '#555';
document.getElementById('defaultOpen3').style.backgroundColor= '#555';
document.getElementById('defaultOpen4').style.backgroundColor= '#777';
document.getElementById('sendsms_recipient').style.display = 'none';
document.getElementById('postbox-container-2').style.display = 'block';
document.getElementById('sendsms_titleA').style.display = 'none';
document.getElementById('sendsms_msg').style.display = 'none';
document.getElementById('sendsms_msg2').style.display = 'none';

document.getElementById('sendsms_choose_recipient').style.display = 'none';
document.getElementById('url_track').style.display = 'none';
document.getElementById('meesageLength').style.display = 'none';
document.getElementById('sendsms_titleB').style.display = 'none';
document.getElementById('cmp_details').style.display = 'block';
document.getElementById('sendsms_buttons').style.display = 'block';

document.getElementById('titleres').innerHTML = ' '+document.getElementById('sendsms_title').value;
document.getElementById('msgres').innerHTML = ' '+document.getElementsByName('sendsms_message')[0].value;
document.getElementById('senddateres').innerHTML = ' '+document.getElementById('sendsms_date').value??'-'+' '+document.getElementById('sendsms_date_hour').value??'-'+':'+document.getElementById('sendsms_date_minute').value??'-';





"><div style ="font-size:20px;color:#765f5f;text-align:center;line-height:0; 0;border-radius:50%;background:white; margin-right: 10px; padding: 7px 14px; display: inline;">4</div>Confirmation</button>
        <div style ="width : 800px; margin: 0 auto!important">
            <form id="sendsms_form" action="<?php echo $this->get_form_url(); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" id="action" name="action" value="<?php echo get_class($this) ?>"/>
                <input type="hidden" id="id_sendsms_campaign" name="id_sendsms_campaign" value="<?php echo $this->_campaign->id_sendsms_campaign; ?>"/>
                <input type="hidden" id="current_status" value="<?php echo $this->_campaign->status ?>"/>

                <div id="poststuff">
                    <div id="post-body"  class="metabox-holder columns-2" style="margin-right: 0;"><!-- body -->

                        <!-- Next step button -->
                        <input id ="next_step" class="btn btn-lg btn-primary float-right" style ="margin-left: 25px; margin-top: 25px;" type="submit" value ="<?php _e('Next Step', 'insim'); ?>" />

                        <div style = "width: 600px; display: none; margin-top: 40px;" id="postbox-container-2" class="postbox-container"><!-- left column-->
                            <div class="postbox">
                                <div class="panel-wrap woocommerce ">
                                    <div id="sendsms_data" class="panel">
                                   <!--<h2><?php printf(__('Campaign %s details', 'woocommerce'), esc_html($this->_campaign->id_sendsms_campaign)); ?></h2>-->

                                        <div class="sendsms_data_column_container">
                                            <div class="sendsms_data_column">
                                                <!-- <h2><span class="dashicons dashicons-admin-settings vmiddle"></span><span><?php ($this->_campaign->status == 0 ? _e('SMS settings', 'insim') : _e('SMS details', 'insim')) ?></span></h2> -->

                                                <?php
                                                if (!$b_auth) {
                                                    echo '<div class="alert alert-danger"> ' . __('Before sending a message, you have to enter your account information in the Settings Tab.', 'insim') . '</div><br/><br/>';
                                                } else {
                                                    echo '';
                                                }
                                                ?>
                                            
                                                <!-- Campaign details -->
                                                <div id= "cmp_details">
                                                    <div class="card" style="box-shadow: 5px 5px lightgrey; margin-bottom: 50px;">
                                                        <div class="card-header">
                                                            <strong>Resume :</strong>
                                                        </div>
                                                        <div class="card-body">
                                                            <p  style= "margin-top: 10px;"><span id ="nbrecres" style =" font-size: large;">Number of recipients : </span><span style="background-color: #f7f1f1;"><?php echo ($this->_campaign->nb_recipients) ?></span>
                                                            </p>
                                                            <br />
                                                            <p  style= "margin-top: 10px;"><span style =" font-size: large;">Title : </span><span id ="titleres" style="background-color: #f7f1f1;"></span>
                                                            </p>
                                                            <br />
                                                            <p  style= "margin-top: 10px;"><span  style =" font-size: large;">Message : </span><span id ="msgres" style="background-color: #f7f1f1;"></span>
                                                            </p>
                                                            <br />
                                                            <p  style= "margin-top: 10px;"> <span style =" font-size: large;">Send date : </span><span id ="senddateres" style="background-color: #f7f1f1;"></span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <style>
                                                        /* input[type=checkbox]*/
                                                        #switch{
                                                            height: 0;
                                                            width: 0;
                                                            visibility: hidden;
                                                        }

                                                        .label {
                                                            cursor: pointer;
                                                            text-indent: -9999px;
                                                            width: 60px;
                                                            height: 30px;
                                                            background: grey;
                                                            display: inline-block;
                                                            border-radius: 100px;
                                                            position: relative;
                                                        }

                                                        .label:after {
                                                            content: '';
                                                            position: absolute;
                                                            top: 5px;
                                                            left: 5px;
                                                            width: 20px;
                                                            height: 20px;
                                                            background: #fff;
                                                            border-radius: 90px;
                                                            transition: 0.3s;
                                                        }

                                                        input:checked + .label {
                                                            background: #2271b1;
                                                        }

                                                        input:checked + .label:after {
                                                            left: calc(100% - 5px);
                                                            transform: translateX(-100%);
                                                        }

                                                        .label:active:after {
                                                            width: 130px;
                                                        }
                                                        </style>
                                                        <style>
                                                            ul.dashed-list {
                                                                list-style-type: none;
                                                                padding-left: 0;
                                                            }

                                                            ul.dashed-list li:before {
                                                                content: '+';
                                                                margin-right: 5px;
                                                                font-weight: bold;
                                                            }
                                                        </style>

                                                    <div id="switch_div" onclick="clickChangeMode();"><input type="checkbox" name="isteam" id="switch" style="margin-top: 25px;" value="<?php echo ($this->_campaign->isteam); ?>" <?php echo ($this->_campaign->isteam == 1) ? 'checked' : ''; ?> <?php echo (get_option('is_team')== true ? '' : 'disabled'); ?> onclick="changeSendMod();"/><label class="label" for="switch">Toggle</label><span style="margin-left: 15px;">Send SMS from multiple devices.</span></div>
                                                    <div style="padding-left: 15px;">Connect multiple mobile phones to your e-store for more speed. (TEAM mode)</div>
                                                    <div style="margin-top: 10px; padding-left: 15px;"> <span class="dashicons dashicons-info"></span><button type="button" style="border: none; background-color: unset;  color: #2271b1; text-decoration: underline;" data-toggle="modal" data-target="#exampleModalCenter">More</button></div>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                            <div class="modal-header" style="display: flex; padding-left: 40%;">
                                                                <h2 class="modal-title" id="exampleModalLongTitle">                                                            
                                                                    <p style="font-size: large;"><b>TEAM Mode</b></p>
                                                                </h2>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p style="font-size: large;"><b>Ardary inSIM integrates the ability to connect multiple cell phones to manage your customer SMS messages.</b></p>
                                                                <div style="text-align: center">
                                                                    <img  src="/wp_services/wp-content/plugins/insim/admin/partials/../img/insim-team-mode.png" style="width: 70%;" alt="inSIM TEAM mode"/>
                                                                </div>
                                                                <p style="font-size: medium; margin-top: 15px;">To do this, download the Ardary inSIM app on each new mobile, then :</p>
                                                                    <ul  class="dashed-list">
                                                                        <li> Create an account for each phone, with a unique email address for each one.</li>
                                                                        <li> From the initial mobile, the one with which you connected your e-store first, go to inSIM app, click the TEAM tab, create a TEAM and add members using their email addresses.</li>
                                                                        <li> In each new mobile, in the inSIM app, TEAM tab, accept the TEAM request.</li>
                                                                    </ul>
                                                                <p style="font-size: medium;">You can now send your campaigns using multiple devices.</p>
                                                            </div>
                                                            <div class="modal-footer" style="justify-content: space-between!important;">
                                                                <div>
                                                                    <div style="font-style: oblique; color: #1aadba; font-weight: bold;">PREMIUM option</div>
                                                                    <div>
                                                                        <p style="margin-top: 0">This is a PREMIUM feature. <a href="https://insim.app/" target ="_blank">Get PREMIUM</a>.</p>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Got it !</button>
                                                                </div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- campaign title -->
                                                <p id="sendsms_titleA" class="form-field form-field-wide">
                                                    <label for="sendsms_title"><span style ="font-size: x-large;"><?php _e('Title of the campaign', 'insim') ?></span></label><br/>
                                                    <input style="margin-top: 20px;" type="text" id="sendsms_title" name="sendsms_title" maxlength="255" value="<?php echo htmlentities($this->_campaign->title, ENT_QUOTES, 'utf-8'); ?>" />
                                                </p>

                                                <!-- campaign message-->
                                                <p id ="sendsms_msg" class="form-field form-field-wide">
                                                    <label for="sendsms_title"><span style ="font-size: x-large;"><?php _e('Message') ?></span></label><br/>
                                                    <br/><span id="sendsms_msg2" style="margin-top: -10px;"><?php _e('Variables you can use : {firstname}, {lastname}', 'insim'); ?></span><br/>                              
                                                    <textarea class="form-control" style="margin-top: -10px;"
                                                    <?php echo ($this->_campaign->status == 0 ? '' : 'readonly') ?>
                                                        rows="5" cols="50" name="sendsms_message" ><?php echo htmlentities($this->_campaign->message, ENT_QUOTES, 'utf-8'); ?></textarea>  
                                                    <div id = "meesageLength" style="margin-left: 517px; margin-top: -33px; display: block!important;">0/160</div>
                                                    <div id ="url_track" style="disply: inline-block; margin-top: 15px;" onclick="clickInsetTrackingUrl();">
                                                        <br><span ><b>Insert trackable URL</b><sup>(1)</sup> <span style="font-style: oblique; color: #1aadba; font-weight: bold;">PREMIUM option</span><sup>(2)</sup></span></br>
                                                        <input id ="url_tracking_value" style="width: 73%; disply: inline-block; margin-top: 15px;" type="text" placeholder="https://www.ardary-insim.com/example">
                                                        <button id ="url_tracking" class="button button-primary"  <?php echo (get_option('is_premium') == false ? 'disabled title ="You can not use this option until you pay a subscription !"' : ''); ?> style="disply: inline-block; height: 30px; margin-top: 15px; cursor: pointer;"><?php _e('Insert in message', 'insim'); ?></button>
                                                        <p id ='url_tracking_alert' style='display: none; color: red; padding-left: 5px;'><b style="font-weight: 900;"></b> You can not use this option until you pay a subscription ! </p>
                                                        <p style="margin-bottom: 0px!important">(1) We create automatically a trackable short URL that allows you to get detailled analytics.</p>
                                                        <?php if ( get_option('is_premium') == false){
                                                            echo '<p style="margin-top: 0">(2) This is a PREMIUM feature. <a href="https://insim.app/" target ="_blank">Get PREMIUM</a>.</p>';
                                                        } else {
                                                            echo '<p style="margin-top: 0">(2) You are currently in the PREMIUM trial period, this feature is available to you.</p>';
                                                          }
                                                        ?>
                                                    </div>                                                    
                                                </p>

                                                <!-- campaign date -->
                                                <div style ="margin-top: 70px; display:inline-block;" id="sendsms_titleB" class="form-field form-field-wide"><label for="sendsms_date"><span style ="font-size: x-large;"><?php _e('Send date', 'insim') ?></span></label><br/>
                                                    <input style="margin-top: 20px; display: inline-block; width: 42%;" type="text" <?php echo ($this->_campaign->status < 2 ? '' : 'readonly') ?> class="date-picker-field datepicker" name="sendsms_date" id="sendsms_date" maxlength="10" value="<?php echo $this->_campaign->date_send != "0000-00-00 00:00:00" && $this->_campaign->date_send !="" ? date_i18n('Y-m-d', strtotime($this->_campaign->date_send)) : '' ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                                                    @<input style="margin-top: 20px; display: inline-block; width: 20%" type="text" <?php echo ($this->_campaign->status < 2 ? '' : 'readonly') ?> class="hour" style="width:2.5em" placeholder="<?php _e('h', 'insim') ?>" name="sendsms_date_hour" id="sendsms_date_hour" maxlength="2" size="2" value="<?php echo $this->_campaign->date_send != "0000-00-00 00:00:00" && $this->_campaign->date_send !="" ? date_i18n('H', strtotime($this->_campaign->date_send)) : '' ?>" pattern="\-?\d+(\.\d{0,})?" />
                                                    :<input style="margin-top: 20px; display: inline-block; width: 20%;" type="text" <?php echo ($this->_campaign->status < 2 ? '' : 'readonly') ?> class="minute" style="width:2.5em" placeholder="<?php _e('m', 'insim') ?>" name="sendsms_date_minute" id="sendsms_date_minute" maxlength="2" size="2" value="<?php echo $this->_campaign->date_send != "0000-00-00 00:00:00" && $this->_campaign->date_send !="" ? date_i18n('i', strtotime($this->_campaign->date_send)) : '' ?>" pattern="\-?\d+(\.\d{0,})?" />
                                                    <br><?php echo __('Time Zone:', 'insim') . ' ' . date_default_timezone_get(); ?>
                                            </div>

                                            </div>
        

                                            <!-- <div class="sendsms_data_column"> -->
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <br class="clear">
                </div>
                <?php
                $this->output_choose_recipient();
                $this->output_button();
                echo "<br/>";
                ?>
            </form>
        </div>
        <style>
            .loader {
                border: 5px solid #f3f3f3; /* Light grey */
                border-top: 5px solid green; /* Blue */
                border-radius: 50%;
                width: 25px;
                height: 25px;
                animation: spin 1s linear infinite;
            }

                @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
                }
        </Style>
       
            <div style ="margin-left: 40px; display: inline-block;" id="sendsms_recipient" class="postbox-container postbox">                
                <?php
                $myListTable = new Sim_To_Shop_Recipient_List(array('campaign' => $this->_campaign));
                echo '<h2 style="display: inline-block"><span class="dashicons dashicons-admin-users vmiddle" style ="margin-right: 10px;"></span>' . __('List of recipients', 'insim') . '</h2>';
                ?>
                 <div id ="load_recipients_list" style ="display: none; position: absolute; margin-top: 10px; margin-left: 20px;" class="loader"></div><span id ="load_recipients_list_text" style="display: none; margin-left: 70px;font-size: larger; color:green;">Loading recipients list ...</Span>
                <?php //$myListTable->search_box('search', 'search_id'); ?>

                <?php
                $myListTable->prepare_items();
                $myListTable->display();
                ?>                
            </div>
            <?php
            //construct specific js script

            if ($this->_campaign->status == 0) {
                echo '<script> var timeText = "' . __('Time', 'insim') . '";var hourText = "' . __('Hour', 'insim') . '";var minuteText = "' . __('Minute', 'insim') . '";var secondText = "' . __('Second', 'insim') . '";var currentText = "' . __('Now', 'insim') . '";var closeText = "' . __('Closed', 'insim') . '";';
            }
            echo ' var sendsms_error_phone_invalid = "' . __('That phone number is invalid.', 'insim') . '";var sendsms_error_csv = "' . __('Please choose a valid CSV file', 'insim') . '";var sendsms_error_orders = "' . __('That number must be greater or equal to 1', 'insim') . '";var sendsms_confirm_cancel = "' . __('Are you sure you want to cancel that campaign ?', 'insim') . '";var sendsms_confirm_delete = "' . __('Are you sure you want to delete that campaign ?', 'insim') . '";';
            if (get_class($this) == 'Sim_To_Shop_Send_Tab' && isset($_REQUEST['sendsms_transmit']) && $this->_campaign->status == 1 && !sizeof(self::$errors)) {
                echo'transmitToOWS();';
            }

            echo'jQuery(document).ready(function() {jQuery(".datepicker").datepicker({dateFormat : "yy-mm-dd"});initTab();}); </script>';

           
        }

        public function output_campaign_details() {
            $balance = Sim_To_Shop_API::get_instance()->get_balance();
            $balance = $balance !== '001' ? $balance : 0;

            echo '
        <style>
            .tooltip {
            position: relative;
            display: inline-block;
            }

            .tooltip .tooltiptext {
            visibility: hidden;
            width: 315px;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 15px 0;
            position: absolute;
            z-index: 1;
            bottom: 150%;
            margin-left: -148px;
            line-height: 20px;
            }

            .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: black transparent transparent transparent;
            }

            .tooltip:hover .tooltiptext {
            visibility: visible;
            }

            .question-mark-icon:after {
                content: url("https://api.iconify.design/dashicons:editor-help.svg?height=20");
            vertical-align: -0.190em;
            }
            #block_infos label{line-height:20px;}

</style>
        <fieldset id="block_infos">
        <h2><span class="dashicons dashicons-info vmiddle"></span>' . __('Information') . '</h2>
        <label><b>' . __('Current Balance', 'insim') . '</b></label>
        <div id="balance">' . number_format((float) $balance, 0, '', ' ') . ' SMS</div>
        <div class="clear"></div>
        <label>' . __('Campaign ID', 'insim') . '</label>
        <div id="id_campaign">' . $this->_campaign->id_sendsms_campaign . '</div>
        <div class="clear"></div>
        <label>' . __('Ticket', 'insim') . '</label>
        <div id="ticket">' . $this->_campaign->ticket . '</div>
        <div class="clear"></div>
        <label>' . __('Status', 'insim') . '</label>
        <div id="status">' . Sim_To_Shop_Admin::get_instance()->get_status($this->_campaign->status) . '</div>
        <div class="clear"></div>' .
            ($this->_campaign->status == 5 ? '
        <label>' . __('Error', 'insim') . '</label>
        <div id="error_code">' . Sim_To_Shop_Admin::get_instance()->get_error_SMS($this->_campaign->error_code) . '</div>
        <div class="clear"></div>' : '') .
            (get_class($this) == 'Sim_To_Shop_History_Tab' && $this->_campaign->simulation ? '
        <label>' . __('Simulation', 'insim') . '</label>
        <div id="simulation">' . __('Yes', 'insim') . '</div>' : '') .
            (get_class($this) == 'Sim_To_Shop_History_Tab' && $this->_campaign->paid_by_customer ? '
        <label>' . __('Paid by customer', 'insim') . '</label>
        <div id="paid_by_customer">' . __('Yes', 'insim') . '</div>' : '') . '
        <div class="clear"></div>
        <label>' . __('Recipients', 'insim') . '</label>
        <div id="nb_recipients">' . $this->_campaign->nb_recipients . '</div>
        <div class="clear"></div>
        <!--<label>' . __('Nb of SMS', 'insim') . '</label>
        <div id="nb_sms">' . $this->_campaign->nb_sms . '</div>
        <div class="clear"></div>-->
        <label>' . __('Price', 'insim') . '</label>
        <div id="price">' . number_format($this->_campaign->price, 3, '.', '') . ' €</div>
        <div class="clear"></div>       
        
        <label class="tooltip">' . __('Send date', 'insim').'
         <span class="tooltiptext">This indicates when your campaign will be delivered</span><span class="question-mark-icon"></span>
        </label>
        <div>' . ($this->_campaign->date_send != "0000-00-00 00:00:00" && $this->_campaign->date_send !="" ? date_i18n('d-m-Y H:i:s', strtotime($this->_campaign->date_send)) : '') . '</div>
        <div class="clear"></div>       
        <label class="tooltip">' . __('Transmission date', 'insim').'
         <span class="tooltiptext">The date when your request was sent to Solo from the module module</span><span class="question-mark-icon"></span>
        </label>     
        <div>' .($this->_campaign->date_transmitted != "0000-00-00 00:00:00" && $this->_campaign->date_transmitted !="" ? date_i18n('d-m-Y H:i:s', strtotime($this->_campaign->date_transmitted)) : '') . '</div>
        <div class="clear"></div>     
        <label class="tooltip">' . __('Validation date', 'insim').'
         <span class="tooltiptext">This is the date you clicked on "Accept & Send"</span><span class="question-mark-icon"></span>
        </label> 
        <div>' . ($this->_campaign->date_validation != "0000-00-00 00:00:00" && $this->_campaign->date_validation !="" ? date_i18n('d-m-Y H:i:s', strtotime($this->_campaign->date_validation)) : '') . '</div>
    </fieldset>';
        }

        function output_choose_recipient() {
            if (get_class($this) == 'Sim_To_Shop_Send_Tab' && $this->_campaign->status == 0) {
                ?>
                <div class="poststuff">
                    <div id="sendsms_choose_recipient" class="postbox">
                        <style>
                #add_recipient {       
                    display: inline-block;
                    outline: 0;
                    cursor: pointer;
                    padding: 5px 16px;
                    font-size: 14px;
                    font-weight: 500;
                    line-height: 20px;
                    vertical-align: middle;
                    border: 1px solid;
                    border-radius: 6px;
                    color: #ffffff;
                    background-color: #2ea44f;
                    border-color: #1b1f2326;
                    box-shadow: rgba(27, 31, 35, 0.04) 0px 1px 0px 0px, rgba(255, 255, 255, 0.25) 0px 1px 0px 0px inset;
                    transition: 0.2s cubic-bezier(0.3, 0, 0.5, 1);
                    transition-property: color, background-color, border-color;
                    }
                    #add_recipient:hover {
                        background-color: #2c974b;
                        border-color: #1b1f2326;
                        transition-duration: 0.1s;
                    }
                        </Style>
                        <input type='hidden' id ='nbrecRes'/>
                        <input type='hidden' id ='messageRess'/>
                        <input type='hidden' id ='titleRes'/>
                        <input type='hidden' id ='dateRes'/>
                        <p style ="font-size: x-large; margin-top: 60px;"><?php _e('Create your sending list :', 'insim') ?></p>
                        <table class="form-table">

                            <tbody>
                               
                                               
                                <tr>
                                            <td class="forminp forminp-text" id="sendsms_query">
                                        <div>
                                            <div style="display:block;float:left; margin-right: 40px;">
                                                <!-- choose a country -->
                                                <div class="filter_label"  style ="margin-bottom: 5px;"><b><?php _e('Country : ', 'insim') ?></b></div>
                                                <select name="sendsms_query_country" id="sendsms_query_country" class="country_to_state country_select" >
                                                    <span>Country : </span>
                                                    <?php
                                                    $wccountries = new WC_Countries();
                                                    $field = '';
                                                    foreach ($wccountries->get_countries() as $ckey => $cvalue)
                                                        $field .= '<option value="' . esc_attr($ckey) . '" >' . __($cvalue, 'woocommerce') . '</option>';
                                                    echo ($field);
                                                    ?>
                                                </select>
                                                <br />
                                                <br />
                                                <br/>
                                                <span style ="padding-left: 38%"><b>AND</b></span>
                                                <br />
                                                <br />
                                                <div class="filter_label"  style ="margin-bottom: 5px;"><b><?php _e('Registration date : ', 'insim') ?></b></div> <?php _e('From', 'insim') ?> <input type="text" class="datepicker" name="sendsms_query_registered_from" size="10" maxlength="10" />
                                                <?php _e('To', 'insim') ?> <input type="text" class="datepicker" name="sendsms_query_registered_to" size="10" maxlength="10" />
                                                <div   style ="margin-top: 5px;"><span class="filter_label" ><?php _e('<span style="text-decoration: underline;">Option </span>: ignore years', 'insim') ?></span> <input type="checkbox" name="sendsms_query_registered_years" value="1" /></div><br>
                                                <br />
                                                <span style ="padding-left: 38%"><b>AND</b></span>
                                                <br />
                                                <br />
                                                <div class="filter_label"  style ="margin-bottom: 5px;"><b><?php _e('Last connection date : ', 'insim') ?></b></div> <?php _e('From', 'insim') ?> <input type="text" class="datepicker" name="sendsms_query_connected_from" size="10" maxlength="10" />
                                                <?php _e('To', 'insim') ?> <input type="text" class="datepicker" name="sendsms_query_connected_to" size="10" maxlength="10" />
                                                <div   style ="margin-top: 5px;"><span class="filter_label"   style ="margin-top: 5px;"><?php _e('<span style="text-decoration: underline;">Option </span>: ignore years', 'insim') ?></span> <input type="checkbox" name="sendsms_query_connected_years" value="1" /></div><br>
                                                <br />
                                                <span style ="padding-left: 38%"><b>AND</b></span>
                                                <br />
                                                <br />
                                                <div class="filter_label"  style ="margin-bottom: 5px;"><b><?php _e('Number of orders', 'insim') ?></b></div> <?php _e('From', 'insim') ?> <input type="text" id="sendsms_query_orders_from" name="sendsms_query_orders_from" size="10" maxlength="10" />
                                                <?php _e('To', 'insim') ?> <input type="text" id="sendsms_query_orders_to" name="sendsms_query_orders_to" size="10" maxlength="10" />
                                                <div   style ="margin-top: 5px;"><span class="filter_label"   style ="margin-top: 5px;"><?php _e('<b>OR</b> no order', 'insim') ?></span> <input type="checkbox" id="sendsms_query_orders_none" name="sendsms_query_orders_none" value="1" /></div>
                                            </div>
                                            <div id ="to_top" style="display:none; position: fixed; bottom: 108px;z-index: 1; right: 0px; padding: 10px; border-radius: 5px;">
                                            </div>
                                            <input id ="next_step2" class="btn btn-lg btn-primary" style ="display:block; position: absolute; top: 147%; z-index: 1; right: -27%;" type="submit" value ="<?php _e('Next Step', 'insim'); ?>" />
                                            <style>
                                                .left-arrow {
                                                    position: relative;
                                                    background: #2271b1;
                                                    padding: 15px;
                                                }
                                                .left-arrow:after {
                                                    content: '';
                                                    display: block;  
                                                    position: absolute;
                                                    right: 100%;
                                                    top: 10%;
                                                    margin-top: -10px;
                                                    width: 0;
                                                    height: 0;
                                                    border-top: 10px solid transparent;
                                                    border-right: 10px solid #ebc836;
                                                    border-bottom: 10px solid transparent;
                                                    border-left: 10px solid transparent;
                                                }
                                            </style>

                                            <div class="card left-arrow" style="background-color: #ebc836; margin-left: 10px; border-radius: 10px 30px; box-shadow: 10px 10px #f3f1f1;">
                                            <div class="arrow-left" ></div>
                                                <div class="card-header" style="font-family: fantasy;">
                                                    Mailing list creation helper <i class="fa fa-question-circle ml-2" aria-hidden="true"></i>
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <p style="font-weight: bolder;">Enter your criteria to filter the customers to whom you send the campaign.</p>
                                                        <ul class="mt-2">
                                                            <li> - All fields are optional.<li>
                                                            <li> - If no field is filled in, all contacts will be selected.<li>
                                                            <li> - Maximum 3000 contacts per campaign.<li>
                                                        </ul>
                                                    </div>
                                                    <div id ="customers_selected" style="z-index: 1; padding: 10px; border-radius: 5px; color: white;">
                                                        <span  style="float: right;"><span id="sendsms_query_result"></span> <?php _e('customer(s) found', 'insim') ?> </span><span style ='display: none; color: red; margin-top: 40px; ' id="sendsms_max"> Maximum recipients number authorized is 3000 !</span>
                                                        <!-- Maximum recipients number authorized is 3000 !</span> -->
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <Button type="button" id="sendsms_query_add" disabled ="disabled" style ="display: block; text-align: center; width:100%;" class="button button-primary"  title="<?php _e('Updating sending list', 'insim') ?>">Add <span id="sendsms_query_result_to_add"></span> recipient(s)</Button>
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                </tr>							                                    
                            </tbody>
                        </table>
                        <?php if ($this->_campaign->status == 0) { ?>
                            <div style="line-height:30px; margin-top: 20px;"><span class="dashicons dashicons-info"></span> <?php _e('All duplicates will be automatically removed', 'insim') ?></div>
                        <?php } ?>

                    </div>
                </div>
                <?php
                echo ('<div style ="visibility: hidden;" id="nb_recipients">' . $this->_campaign->nb_recipients . '</div>');
            }
        }
        public function output_button() {
         
            $b_auth = get_option('solo_sms_email') ? true : false;
            
            ?>
            <div style = "display: none" id="sendsms_buttons">
                <div id="buttons" class="clear center" style="display: <?php (isset($_REQUEST['sendsms_transmit']) && $this->_campaign->status == 1 && !sizeof(self::$errors) ? 'none' : 'flex') ?>; justify-content: start;">
                    <?php if (get_class($this) == 'Sim_To_Shop_Send_Tab') { ?>
                        <input type="submit" id="sendsms_save" name="sendsms_save" style="background-color: #2271b1;" value="<?php _e('Save campaign', 'insim') ?>" class="btn btn-sm  btn-primary" />
                            <input style ="/*color: white; background-color: darkgreen;*/margin-left: 280px;" type="button" id="sendsms_validate" name="sendsms_validate" value="<?php _e('Accept & Send', 'insim') ?>" class="btn  btn-success" /> 
                        
                        <?php
                    }
                    if ($this->_campaign->status >= 1 || $this->_campaign->status < 3 || ($this->_campaign->status == 3 && the_date('Y-m-d H:i:s') < $this->_campaign->date_send)) {
                        ?>
                        <!-- <input <?php (!$b_auth ? 'disabled' : '') ?> type="submit" id="sendsms_cancel" name="sendsms_cancel" value="<?php _e('Cancel this campaign', 'insim') ?>" class="button button-primary" /> -->
                    <?php } ?>
                    <p style="margin-top: 15px;" id="sendsms_other_options"><em>Other options: </em></p>
                    <input style ="" type="submit" id="sendsms_delete" name="sendsms_delete" value="<?php _e('Delete', 'insim') ?>" class="btn btn-sm btn-outline-danger" /> 
                    <?php if ($this->_campaign->event == 'sendsmsFree') { ?>
                        <input type="submit" id="sendsms_duplicate" name="sendsms_duplicate" value="<?php _e('Duplicate campaign', 'insim') ?>" class="btn btn-sm  btn-outline-secondary" />
                    <?php } ?>
                </div>
			</div>
            <?php
            if (get_class($this) == 'Sim_To_Shop_Send_Tab' && isset($_POST['sendsms_transmit']) && $this->_campaign->status == 1 && !sizeof(self::$errors)) {
                echo '<div id="progress_bar" class="error">' . __('Transfer in progress :', 'insim') . ' <span id="waiting_transfert">' . $this->_campaign->nb_recipients . '</span> ' . __('remaining recipients', 'insim') . '</div>';
            }

            ?>
            <div id="loading-modal" class="modalLoading">
                <div class="modal-contentLoading">
                    Please wait ...
                    <div class="loaderLoading"></div>
                </div>
            </div>
            <script>
                document.getElementById('sendsms_query_add').addEventListener('click', function() {
                    document.getElementById('sendsms_validate').setAttribute('disabled', 'disabled');
                    document.getElementById('sendsms_validate').setAttribute('title', 'You have to save the modifications first !');
                });
                document.getElementsByName('sendsms_message')[0].addEventListener('input', function() {
                    document.getElementById('sendsms_validate').setAttribute('disabled', 'disabled');
                    document.getElementById('sendsms_validate').setAttribute('title', 'You have to save the modifications first !');
                });
                document.getElementById('url_tracking_value').addEventListener('input', function() {
                    document.getElementById('sendsms_validate').setAttribute('disabled', 'disabled');
                    document.getElementById('sendsms_validate').setAttribute('title', 'You have to save the modifications first !');
                });
                document.getElementById('sendsms_title').addEventListener('input', function() {
                    document.getElementById('sendsms_validate').setAttribute('disabled', 'disabled');
                    document.getElementById('sendsms_validate').setAttribute('title', 'You have to save the modifications first !');
                });
                document.getElementById('sendsms_date').addEventListener('input', function() {
                    document.getElementById('sendsms_validate').setAttribute('disabled', 'disabled');
                    document.getElementById('sendsms_validate').setAttribute('title', 'You have to save the modifications first !');
                });
                jQuery(document).ready(function($) {
                    jQuery("#sendsms_date").datepicker({
                        dateFormat: 'yy-mm-dd',
                        onSelect: function() {
                            jQuery(this).change();
                        }
                    });

                    jQuery('#sendsms_date').on('change', function() {
                        document.getElementById('sendsms_validate').setAttribute('disabled', 'disabled');
                        document.getElementById('sendsms_validate').setAttribute('title', 'You have to save the modifications first !');
                    });
                });
                document.getElementById('sendsms_date_hour').addEventListener('input', function() {
                    document.getElementById('sendsms_validate').setAttribute('disabled', 'disabled');
                    document.getElementById('sendsms_validate').setAttribute('title', 'You have to save the modifications first !');
                });
                document.getElementById('sendsms_date_minute').addEventListener('input', function() {
                    document.getElementById('sendsms_validate').setAttribute('disabled', 'disabled');
                    document.getElementById('sendsms_validate').setAttribute('title', 'You have to save the modifications first !');
                });
                var stp =1;
        jQuery(document).ready(function($) {
            if( "<?php echo (get_option('is_premium') == false ? true : false); ?>" ){
                document.getElementById('url_tracking_value').addEventListener('input', (e) => {
                    document.getElementById('url_tracking_alert').style.display = 'inline-block';
                });
            }
            if (document.getElementById('message') != null && document.getElementById('message') != undefined){
                document.getElementById('next_step').style.top = "300px";
                undefined != document.getElementById('newcmp') && null != document.getElementById('newcmp') ? document.getElementById('newcmp').style.marginTop = "230px" : '';
            }
            
            function nextStep(step){
                document.getElementById('defaultOpen'+step).click();
            }

            document.getElementById('next_step').addEventListener('click', (e) => {
                e.preventDefault();
                if (stp == 1 ){
                    nextStep(2);
                } else if(stp == 2){
                    nextStep(3);
                } else if(stp ==3){
                    nextStep(4);
                   document.getElementById('next_step').style.display = "none";

                }
            })
            document.getElementById('next_step2').addEventListener('click', (e) => {
                e.preventDefault();
                if (stp == 1 ){
                    nextStep(2);
                } else if(stp == 2){
                    nextStep(3);
                } else if(stp ==3){
                    nextStep(4);
                   document.getElementById('next_step2').style.display = "none";

                }
            })
            jQuery('#sendsms_validate').click(function (e) {
                //console.log('11');
                var id="", destinataires = [];
                var nom = "<?php echo $this->_campaign->title; ?>";
                var msg = decodeURI("<?php echo urlencode($this->_campaign->message); ?>").replaceAll('\r\n', '\n').replaceAll('+',' ');
                var dateSend = "<?php echo $this->_campaign->date_send; ?>";

                //firstname: firstname, lastname: lastname, à vérifier pb !
                var receipients = <?php echo json_encode(get_option('recipients')); ?>;
                //console.log(receipients[0]);
                receipients.forEach(element => destinataires.push({firstname: element.firstname, lastname: element.lastname, phone_num: element.phone, registred:'false', liste_id:null, if_liste:null, type:"contact", phone_id:null}));
                var isteam = "<?php if (get_option('is_team')) {echo $this->_campaign->isteam;} else {echo 0;} ?>";
                var tt = "<?php echo get_option('solo_sms_key'); ?>";
                msg = msg.replaceAll('{firstname}', '[nom_contact]').replaceAll('{lastname}', '[prenom_contact]').replace(/\+/g, ' ')
                        .replace(/%3A/g, ':')
                        .replace(/%2F/g, '/')
                        .replace(/%40/g, '@')
                        .replace(/%2C/g, ',')
                        .replace(/%3B/g, ';');
                //console.log(msg);
                var fstind= msg.indexOf('%23');
                var lstind = msg.lastIndexOf('%23');
                //console.log(fstind);
                //console.log(lstind);
                var url_tracking = fstind != -1 ? msg.substring(fstind+3, lstind) : '';
                if (fstind != -1) msg = msg.replaceAll('{firstname}', '[nom_contact]').replaceAll('{lastname}', '[prenom_contact]').replaceAll(msg.substring(fstind, lstind+3), '[Lien_Tracking]');
                //console.log(msg);
                let sendUrl = 'https://www.ardary-sms.com/api/add_campaign.php';
                    //event.preventDefault();
                    var data = {
                        key: tt,
                        campagne : {id: id , message: msg , nom: nom , date_send: dateSend, url_hcode: decodeURI(url_tracking), destinataires: JSON.stringify(destinataires), 
                            priorite:0, link:"", type_vcard:"", filename:"", isteam },

                    };
                    //console.log(data);
                    $.post(sendUrl, data, function(response) {
                        // console.log(data);
                         //console.log('reponse: '+JSON.stringify(response));
                         var error = JSON.parse(JSON.stringify(response)).data.problem;
                         if(error != undefined && error != null){
                            alert('This campaign name already exist !');
                            // e.stopPropagation();
                            // e.preventDefault();

                         }
                         else {
                            alert('Campaign has been sent successfully.');
                            let idsms = "<?php echo ($this->_campaign->id_sendsms_campaign); ?>";
                            let sendUrl2 = "<?echo  admin_url( 'admin-ajax.php?action=inSIM_get_data')?>";
                    //event.preventDefault();
                    var data = {
                        action: 'inSIM_get_data',
                        id : parseInt(idsms)
                    };
                    $.post(sendUrl2, data, function(response) {
                           //console.log(response);

                         });
                        document.location.href = "<?echo  admin_url( 'admin.php?page=insim&tab=campaigns')?>";
                        }
                    })
                    .fail( function(xhr, textStatus, errorThrown) {
                        alert('An error has been accured, please try send again !');
                    });
                

	       
            });

            
        });
                function changeSendMod(){
                    const clientPermission = "<?php  echo (get_option('is_team') ? true : false );?>";
                    if (clientPermission){
                        const swtch = document.getElementById('switch');
                        swtch.value = swtch.value =="0" ? "1" : "0";
                    }
                }

                function clickChangeMode(){
                    const clientMail = "<?php echo get_option('solo_sms_email');?>";
                    const adminPhone = "<?php echo get_user_meta(get_current_user_id(),'phone_number',true);?>";
                    const shopUrl = "<?php  echo get_permalink( wc_get_page_id( 'shop' ));?>";
                    const pluginVersion = "1.3.0";
                    const type = "e-shop";
                    const name = "wooc";
                    const source = "Send campaign by team members";
                    let sendUrl = 'https://www.ardary-sms.com/api/interrested_in_paid_option.php';
                    //event.preventDefault();
                    var data = {
                        clientMail,
                        adminPhone,
                        shopUrl,
                        pluginVersion,
                        type,
                        name,
                        source
                    };
                    //console.log(data);
                    jQuery.post(sendUrl, data, function(response) {
                    })
                    .fail( function(xhr, textStatus, errorThrown) {
                    });
                }

                   function clickInsetTrackingUrl(){
                    const clientMail = "<?php echo get_option('solo_sms_email');?>";
                    const adminPhone = "<?php echo get_user_meta(get_current_user_id(),'phone_number',true);?>";
                    const shopUrl = "<?php  echo get_permalink( wc_get_page_id( 'shop' ));?>";
                    const pluginVersion = "1.3.0";
                    const type = "e-shop";
                    const name = "wooc";
                    const source = "Insert trackable URL into campaign sms";
                    let sendUrl = 'https://www.ardary-sms.com/api/interrested_in_paid_option.php';
                    //event.preventDefault();
                    var data = {
                        clientMail,
                        adminPhone,
                        shopUrl,
                        pluginVersion,
                        type,
                        name,
                        source
                    };
                    //console.log(data);
                    jQuery.post(sendUrl, data, function(response) {
                    })
                    .fail( function(xhr, textStatus, errorThrown) {
                    });
                }
            </script>
            <?php
        }

    }

}
