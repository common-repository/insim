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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */
if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly
}

if (!class_exists('Sim_To_Shop_Messages')) :

	/**
	 * Sim_To_Shop_Messages
	 * Tab to configure the sms send in fonction of the events.
	 */
	class Sim_To_Shop_Messages extends WC_Settings_Page
	{

		/**
		 * Constructor.
		 */
		public function __construct()
		{
			
		}

		/**
		 * Display tab and save setting if post data is received
		 */
		public function getBody()
		{

			if( get_option('is_premium') == false){
				echo '
				<style>
				.notice-success, div.updated {
					border-left-color: #f2c471;
				}
				</style>
				
				<div style= "margin-top: 30px;
				display: flex;
				justify-content: space-between;
				align-items: center;
				max-width: 999px;
				width: 95%;background-color: orange;" id="message" class="updated"><div style ="display: flex;
				justify-content: start;
				align-items: baseline; margin-left: 20px;"><h2 style="margin-right: 5px;"><strong>' . __('PREMIUM feature :
				','insim') . '</strong></h2>
				<b> subscribe to inSIM PREMIUM to activate SMS automation features.</b>
				</div><button class= "button-primary" style = "height: 40px; margin-bottom: 15px;margin-top: 15px;" type ="button" formtarget= "_blank" onClick= "javascript:window.open(\'https://insim.app/\', \'_blank\')">Get PREMIUM <i class="fa fa-send"></i></button></div>';
				//die();
			}
			// echo '
			            
			
			//   <div style= "width:85%;" id="message" class="updated"><h2 style="margin-top: 30px;><strong">
			//   <div style="font-size: 16px;
			//   font-weight: bold;
			//   color: green; margin-bottom: 15px;" >Available soon</div>'
			//   . __('Set up the automatic sending of SMS to your customers :
			// ','insim') . '</strong></h2>
			// <style>
			// 	#image {
			// 		line-height: 1.5em;
			// 	}
			// 	ul{
			// 		padding-left: 30px;
			// 		font-size: larger;
			// 	}
			// </style>
			
			// <ul id="image">
			// <li><img width= "20px;" height="20px" style="padding-right: 20px;" src="'.plugin_dir_url(__FILE__).'/img/coming-soon-icon.webp)"/>Welcome message</li>
			// <li><img width= "20px;" height="20px;" style="padding-right: 20px;" src="'.plugin_dir_url(__FILE__).'/img/coming-soon-icon.webp)"/>Order confirmation</li>
			// <li><img width= "20px;" height="20px;" style="padding-right: 20px;" src="'.plugin_dir_url(__FILE__).'/img/coming-soon-icon.webp)"/>Shipping confirmation</li>
			// <li><img width= "20px;" height="20px;" style="padding-right: 20px;" src="'.plugin_dir_url(__FILE__).'/img/coming-soon-icon.webp)"/>Order status updates</li>
			// <li><img width= "20px;" height="20px;" style="padding-right: 20px;" src="'.plugin_dir_url(__FILE__).'/img/coming-soon-icon.webp)"/>and many others ...</li>
			// </ul>
			// <div style="padding-left: 81%;">
			// <b>All these features will coming soon.</b>
			// <br/><i style ="padding-left: 110px;">Wait for us..</i>
			// </div>
			// </div>';
			// return;
		// 	//save is date is send
			$this->_post_process();
			WC_Admin_Settings::show_messages();
			//$defaultLanguage = (int) $this->context->language->id;

			$admin_html = '';
			//display messages for each possible admin hook
			// foreach (Sim_To_Shop_Admin::get_instance()->admin_config as $hookId => $hookName)
			// {
			// 	$admin_html .= $this->_get_code($hookId, $hookName, true);
			// }

			$customer_html = '';
			foreach (Sim_To_Shop_Admin::get_instance()->customer_config as $hookId => $hookName)
			{
				if ($hookId != 'action_order_status_update')
				{
					if ($hookId == 'action_validate_order' || $hookId == 'action_admin_orders_tracking_number_update')
					{
						$customer_html .= $this->_get_code($hookId, $hookName, false, null, true);
					}
					else
					{
						$customer_html .= $this->_get_code($hookId, $hookName, false);
					}
				}
				else
				{
					//specific hook when status of a command change
					global $wp_post_statuses;
					foreach ($wp_post_statuses as $key => $value)
					{
						if (strstr($key, 'wc-'))
						{
							//echo "$key => ".$value->label;//print_r($value,true);
							$customer_html .= $this->_get_code($hookId . "_$key", "Order " . "$value->label", false, null, true);
							//$customer_html .= $this->_get_code($hookId . "_$key", $hookName . " ($value->label)", false, null, true);

						}
					}
				}
			}

			$html = '<style>
			.message_textarea {
				width: 90%;
			}
			.text_td {
				width: 25%;
				vertical-align: top;
			}
			.data_td {
				width: 50%;
			}
			.sms {
				border: 1px solid #e5e5e5;
				/* -webkit-box-shadow: 0 1px 1px rgb(0 0 0 / 4%); */
				box-shadow: 0 1px 1px rgb(0 0 0 / 4%);
				background: #fff;
			}
			table.messages_data {
				max-width: 1024px;
				width: 100%;
			}
			</style>
			<div id="' . get_class($this) . '">
				<br /><h1>' . __('Edit and activate the SMS you want to start sending.', 'insim') . '</h1>';
			$html .=
				'
			<div class="clear"></div>
				<div class="wrap woocommerce">
					<form id="' . get_class($this) . '_form" method="post">
						<input type="hidden" name="action" value="' . get_class($this) . '"/>
						'//<h3>' . __('SMS that will be sent to Admin', 'insim') . '</h3>
						 //<div class="os_row">' .
						// 	$admin_html . '
						// </div>
						// <br />
						// <input name="save" class="button-primary" type="submit" value="' . __('Update', 'insim') . '" />
						// <br /><br />
						.'<h3>' . __('SMS that will be sent to users', 'insim') . '</h3>
						<div class="os_row">' .
							$customer_html . '
						</div>
						<br />
						<input class="button-primary" type="submit" name="save2" value="' . __('Update', 'insim') . '" class="button" />
						<input class="button-primary" type="submit" name="resettxt" value="' . __('Reset all messages', 'insim') . '" class="button" />
					</form>
				</div>
			</div>';

			return $html;
		}

		/**
		 * Get html fragment for this hook
		 * @param type $hookId the id of the hook
		 * @param type $hookName the short description of the hook
		 * @param type $bAdmin
		 * @param type $comment
		 * @param type $bPaid
		 * @return string
		 */
		private function _get_code($hookId, $hookName, $bAdmin = false, $comment = null, $bPaid = false)
{
    //$defaultLanguage = (int)$this->context->language->id;
    //key option name for isactive
    $keyActive  = Sim_To_Shop_Admin::get_instance()->_get_isactive_hook_key($hookId, $bAdmin);
    //'Sim_To_Shop_isactive_' . $hookId . (($bAdmin) ? '_admin' : '');
    //To test with dummy values
    $values     = Sim_To_Shop_API::get_instance()->get_sms_values_for_test($hookId);

    $code = '
    <div class="ows mt-1">
        <script>
            function enableAutomation() {
                const clientMail = "' . esc_js(get_option('solo_sms_email')) . '";
                const adminPhone = "' . esc_js(get_user_meta(get_current_user_id(), 'phone_number', true)) . '";
                const shopUrl = "' . esc_js(get_permalink(wc_get_page_id('shop'))) . '";
                const pluginVersion = "1.3.0";
                const type = "e-shop";
                const name = "wooc";
                const source = "Try to use automations";
                let sendUrl = "https://www.ardary-sms.com/api/interrested_in_paid_option.php";
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
                jQuery.post(sendUrl, data, function(response) {})
                    .fail(function(xhr, textStatus, errorThrown) {});
            }
        </script>
        <table class="messages_data">
            <tr valign="top" class="sms" style="" onclick="enableAutomation()">
                <th scope="row" class="titledesc text_td  p-3">
                    <label for="Sim_To_Shop_email">' . __($hookName, 'insim');

    //if option is not free and the customer pays for it
    if ($bPaid && (int) get_option('Sim_To_Shop_freeoption') == 0) {
        $code .= '<br/><span style="font-weight: normal">' . __('Sent only if the customer pays the option', 'insim') . '</span>';
    }
    $isPaid = get_option('is_premium') == false ? 'disabled title = "You have to pay your subscription first !" ' : '';
    $code .= '<br/><input ' . $isPaid . (get_option($keyActive) == 1 ? 'checked' : '') . ' type="checkbox" name="' . $keyActive . '" value="1"/> <span style="font-weight:normal">' . __('Active', 'insim') . '</span><br/>';
    $code .= '</label>
                </th>
                <td class=" pb-3 forminp forminp-' . $hookId . ' data_td"><br/>';

    $key = Sim_To_Shop_Admin::get_instance()->_get_hook_key($hookId, $bAdmin);
    $txt = Sim_To_Shop_API::get_instance()->replace_for_GSM7(get_option($key) ? get_option($key) : Sim_To_Shop_API::get_instance()->get_sms_default_text($hookId, $bAdmin));
    //TODO test
    $txt_test = Sim_To_Shop_API::get_instance()->replace_for_GSM7(str_replace(array_keys($values), array_values($values), $txt));
    $bGSM7 = Sim_To_Shop_API::get_instance()->is_GSM7($txt_test);

    $code .= '<textarea name="' . $key . '" rows="4" class="message_textarea">' . $txt
        . '</textarea>
        <br/><span class="description">' .
        (!$bGSM7 ? '<img src="../img/admin/warning.gif"> ' . __('This message will be divided into 70 chars parts, because of non-standard chars: ', 'insim') . ' ' . Sim_To_Shop_API::get_instance()->not_GSM7($txt_test) : __('This message will be divided into 160 characters parts', 'insim')) .
        '</span>'
        . '<br/>';
    $code .= '<span class="description">' . __('Variables you can use: ', 'insim') . ' ' . implode(', ', array_keys($values)) . '</span>                                
             </td>
             <td class="p-3 forminp forminp-' . $hookId . '-example" class="text_td">'.
        __('<b>Preview</b>', 'insim').
        
        '<style>
        .telnewsim {
            border-left: 5px solid rgb(86, 85, 85);
            border-right: 5px solid rgb(86, 85, 85);
            border-bottom: 5px solid rgb(86, 85, 85);
            border-radius: 0 0 12px 12px;
            background-color: #f2eeee;
            width: 200px;
            height: 100px;
            position: relative;
        }
        .smsnewsim {
            border-radius: 6px;
            background-color: rgb(29 168 183);
            color: white;
            position: absolute;
            bottom: 10px;
            right: 5px;
            width: 160px;
            padding:2px 5px;
            max-height: 70px;
            overflow: auto;
        }
        .bottom-button-wrapper {
            position: absolute;
            bottom:-3px;
            height:10px;
            width:100%;
        }
        .bottom-button {
            height:10px;
            width:50px;
            border-radius:20px 20px 0 0;
            background-color: rgb(86, 85, 85);
            margin: 0 auto;
        }
        </style>
        <div class="telnewsim"><div class="smsnewsim">' . $txt_test . '</div><div class="bottom-button-wrapper"><div class="bottom-button"></div></div></div>

        '
        ;
    $code .= '</td>
    </tr>
    </table>
    </div>';
    //no multilingual support
    
    return $code;
}


	// 	/**
	// 	 * Update option corresponding to the hook
	// 	 * @param type $hook
	// 	 * @param type $b_admin
	// 	 */
		public function update_message_option($hook, $b_admin = false)
		{
			//if is active
			if ( get_option('is_premium') != false ){
				$hook_is_active = Sim_To_Shop_Admin::get_instance()->_get_isactive_hook_key($hook, $b_admin);
				if (array_key_exists($hook_is_active, $_POST))
				{
					$value = wc_clean($_POST[$hook_is_active]);
					//save the option
					update_option($hook_is_active, (int) $value);
				}
				else
				{
					update_option($hook_is_active, 0);
				}
			}
			//message text
			$hook_key = Sim_To_Shop_Admin::get_instance()->_get_hook_key($hook, $b_admin);
			if (array_key_exists($hook_key, $_POST))
			{
				$value = stripslashes($_POST[$hook_key]);
				//save the option
				update_option($hook_key, Sim_To_Shop_API::get_instance()->replace_for_GSM7(trim($value)));
			}
			//specific case of 'action_order_status_update'
			if ($hook == 'action_order_status_update')
			{
				global $wp_post_statuses;
				foreach ($wp_post_statuses as $key => $value)
				{
					if (strstr($key, 'wc-'))
					{
						$this->update_message_option($hook . "_$key", $b_admin);
					}
				}
			}
		}

		/**
		 * Save data
		 */
		private function _post_process()
		{
			//. __('Reset all messages', 'insim') 
			//if update value
			if (array_key_exists('save', $_POST) || array_key_exists('save2', $_POST))
			{
				foreach (Sim_To_Shop_Admin::get_instance()->admin_config as $hook => $hookName)
				{
					$this->update_message_option($hook, true);
				}
				foreach (Sim_To_Shop_Admin::get_instance()->customer_config as $hook => $hookName)
				{
					$this->update_message_option($hook, false);
				}
			}

			//if reset value
			if (array_key_exists('resettxt', $_POST))
			{
				foreach (Sim_To_Shop_Admin::get_instance()->admin_config as $hook => $hookName)
				{
					//message text
					$hook_key = Sim_To_Shop_Admin::get_instance()->_get_hook_key($hook, true);
					update_option($hook_key, Sim_To_Shop_API::get_instance()->replace_for_GSM7(Sim_To_Shop_API::get_instance()->get_sms_default_text($hook, true)));
				}
				foreach (Sim_To_Shop_Admin::get_instance()->customer_config as $hook => $hookName)
				{
					//message text
					if ($hook != 'action_order_status_update')
					{
						$hook_key = Sim_To_Shop_Admin::get_instance()->_get_hook_key($hook, false);
						update_option($hook_key, Sim_To_Shop_API::get_instance()->replace_for_GSM7(Sim_To_Shop_API::get_instance()->get_sms_default_text($hook)));
					}
					else
					{
						//specific hook when status of a command change
						global $wp_post_statuses;
						foreach ($wp_post_statuses as $key => $value)
						{
							if (strstr($key, 'wc-'))
							{
								//echo "$key => ".$value->label;//print_r($value,true);
								$hook_status = $hook . "_$key";
								$hook_key	 = Sim_To_Shop_Admin::get_instance()->_get_hook_key($hook_status, false);
								update_option($hook_key, Sim_To_Shop_API::get_instance()->replace_for_GSM7(Sim_To_Shop_API::get_instance()->get_sms_default_text($hook_status)));
							}
						}
					}
				}
			}
		}

	 }

	 endif;

return new Sim_To_Shop_Messages();

