<?php

/**
 * Librairie d'envoi de SMS via POST HTTP
 *
 * Auteur Yoni Guimberteau yoni@octopush.com
 *
 * copyright (c) 2014 Yoni Guimberteau
 * licence : utilisation, modification, commercialisation.
 * L'auteur ainsi se decharge de toute responsabilite
 * concernant une quelconque utilisation de ce code, livre sans aucune garantie.
 * Il n'est distribue qu'a titre d'exemple de fonctionnement du module POST HTTP de OCTOPUSH,
 * Vous pourrez toutefois telecharger une version actualisee sur www.octopush.com
 */
include_once ('config.inc.php');

class SMS
{

	public $user_login; // string
	public $api_key;   // string
	public $sms_text; // string
        public $user_lang; //string for news api
	public $sms_recipients;  // array
	public $recipients_first_names;  // array
	public $recipients_last_names;  // array
	public $sms_fields_1;  // array
	public $sms_fields_2;  // array
	public $sms_fields_3;  // array
	public $sms_mode;  // int (instantanous or deffered)
	public $sms_type;  // int (standard or pro)
	public $sending_date;  // int
	public $sms_sender;   // string
	public $request_mode;   // string
	public $request_id;   // string
	public $sms_ticket;   // string
	public $with_replies; // int
	public $transactional; // int
	public $msisdn_sender; // int
	public $request_keys; // int
	// SMS parts
	public $user_batch_id; // string
	public $finished; // int
	public $action; // string

	public function __construct()
	{
		$this->user_login	 = '';
		$this->api_key		 = '';
                $this->user_lang	 = '';
		$this->sms_text = '';

		$this->sms_recipients			 = array();
		$this->recipients_first_names	 = array();
		$this->recipients_last_names	 = array();
		$this->sms_fields_1				 = array();
		$this->sms_fields_2				 = array();
		$this->sms_fields_3				 = array();

		$this->sms_mode		 = inSIM_INSTANTANE;
		$this->sending_date	 = time();
		$this->sms_d		 = date('d');
		$this->sms_m		 = date('m');
		$this->sms_h		 = date('H');
		$this->sms_i		 = date('i');
		$this->sms_y		 = date('Y');

		$this->sms_sender	 = 'CampagneSMS';
		$this->sms_type		 = inSIM_SMS_STANDARD;
		$this->sms_mode		 = inSIM_INSTANTANE;
		$this->request_mode	 = inSIM_REEL;
		$this->request_id	 = '';
		$this->with_replies	 = 0;
		$this->transactional = 0;
		$this->msisdn_sender = 0;
		$this->request_keys	 = '';

		// SMS parts
		$this->user_batch_id = '';
	}

	public function send($params)
	{
		$domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_SMS;
		$port	 = inSIM_PORT;
		//$this->sms_text = $this->sms_text.'{shopurl}'.'{currency}'.'{total_paid}'.'{payment}';
		$order = wc_get_order( $params['order_id'] );
		$message = str_replace('{order_id}', $params['order_id'], $this->sms_text);
		$message = str_replace('{order_state}', $params['new_status'], $message);
		$message = str_replace('{firstname}', $order->get_billing_first_name() != '' ? $order->get_billing_first_name() : $order->get_shipping_first_name(), $message);
		$message = str_replace('{lastname}', $order->get_billing_last_name() != '' ? $order->get_billing_last_name() : $order->get_shipping_last_name(), $message);
		$message = str_replace('{shopname}',  get_bloginfo(), $message);
		$message = str_replace('{shopurl}',  get_permalink( wc_get_page_id( 'shop' )), $message);
		$message = str_replace('{currency}',  $order->get_currency(), $message);
		$message = str_replace('{total_paid}',  $order->get_total(), $message);
		$message = str_replace('{payment}',  $order->get_payment_method(), $message);

		$data = array(
			'key'                    => get_option('solo_sms_key'),
			'email'                  => get_option('solo_sms_email'),
			'frompluguin' 			 => true ,
			//'user_login'			 => $this->user_login,
			//'api_key'				 => $this->api_key,
			'sms_text'				 => $this->sms_text,
			'message'                => $message,
			'first_name'			 => $order->get_billing_first_name() != '' ? $order->get_billing_first_name() : $order->get_shipping_first_name(),
			'lastname'               => $order->get_billing_last_name() != '' ? $order->get_billing_last_name() : $order->get_shipping_last_name(),
			'phone'                  => $order->get_billing_phone() != '' ? $order->get_billing_phone() : $order->get_shipping_first_name(),
			//'sms_recipients'		 => implode(',', $this->sms_recipients),
			//'recipients_first_names' => implode(',', $this->recipients_first_names),
			//'recipients_last_names'	 => implode(',', $this->recipients_last_names)
			// 'sms_fields_1'			 => implode(',', $this->sms_fields_1),
			// 'sms_fields_2'			 => implode(',', $this->sms_fields_2),
			// 'sms_fields_3'			 => implode(',', $this->sms_fields_3),
			// 'sms_mode'				 => $this->sms_mode,
			// 'sms_type'				 => $this->sms_type,
			// 'sms_sender'			 => $this->sms_sender,
			// 'request_mode'			 => $this->request_mode,
			// 'request_id'			 => $this->request_id,
			// 'with_replies'			 => $this->with_replies,
			// 'transactional'			 => $this->transactional,
			// 'msisdn_sender'			 => $this->msisdn_sender
		);
		if ($this->sms_mode == inSIM_DIFFERE)
		{
			// GMT + 1 (Europe/Paris)
			$data['sending_date'] = $this->sending_date;
		}

		// Si des champs sont définis, on calcule la clé
		if ($this->request_keys !== '')
		{
			$data['request_keys']	 = $this->request_keys;
			$data['request_sha1']	 = $this->_get_request_sha1_string($this->request_keys, $data);
		}

		$urlAdd = "https://www.ardary-sms.com/newsite/trialApiSolo.php";
        $dataAdd = [
                'accesskey' => get_option('solo_sms_key'),
                'login' =>  get_option('solo_sms_email') ,
				'message' => $data['message'],
				'phonenumber' => $data['phone']
           
        ];
            $args = array(
                'body'        => $dataAdd,
                'timeout'     => 45,
                'sslverify'   => false,
            );

            $res = wp_remote_post( $urlAdd, $args );
			return true;
// echo"<pre>";
// print_r($res);
// print_r($args);
// print_r($data);
// echo"</pre>";
// die();
		//return trim($this->_httpRequest($domain, $path, $port, $data));
	}

	public function sendSMSParts()
	{
		$domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_SMS_PARTS;
		$port	 = inSIM_PORT;

		$data = array(
			'user_login'			 => $this->user_login,
			'api_key'				 => $this->api_key,
			'sms_text'				 => $this->sms_text,
			'sms_recipients'		 => implode(',', $this->sms_recipients),
			'recipients_first_names' => implode(',', $this->recipients_first_names),
			'recipients_last_names'	 => implode(',', $this->recipients_last_names),
			'sms_fields_1'			 => implode(',', $this->sms_fields_1),
			'sms_fields_2'			 => implode(',', $this->sms_fields_2),
			'sms_fields_3'			 => implode(',', $this->sms_fields_3),
			'sms_mode'				 => $this->sms_mode,
			'sms_type'				 => $this->sms_type,
			'sms_sender'			 => $this->sms_sender,
			'request_mode'			 => $this->request_mode,
			'request_id'			 => $this->request_id,
			'with_replies'			 => $this->with_replies,
			'msisdn_sender'			 => $this->msisdn_sender,
			'user_batch_id'			 => $this->user_batch_id,
			'finished'				 => $this->finished
		);
                
		return trim($this->_httpRequest($domain, $path, $port, $data));
	}

	/*
	 * Fonction SMSBatchAction
	 * 
	 * Allow to send ("send"), to delete ("delete") or to get the status ("status") of a campaign
	 * sent with sendSMSParts
	 * 
	 */

	public function SMSBatchAction($action)
	{
		$domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_BATCH;
		$port	 = inSIM_PORT;

		$data = array(
			'user_login'	 => $this->user_login,
			'api_key'		 => $this->api_key,
			'action'		 => $action,
			'user_batch_id'	 => $this->user_batch_id,
                        'sending_time'  => $this->sending_date
		);

		return trim($this->_httpRequest($domain, $path, $port, $data));
	}

	/*
	 * Fonction create_sub_account
	 * 
	 */

	public function create_sub_account($first_name, $last_name, $raison_sociale, $alert_bound, $alert_sms_type)
	{
		$domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_SUB_ACCOUNT;
		$port	 = inSIM_PORT;

		$data = array(
			'user_login'	 => $this->user_login,
			'api_key'		 => $this->api_key,
			'first_name'	 => $first_name,
			'last_name'		 => $last_name,
			'raison_sociale' => $raison_sociale,
			'alert_bound'	 => intval($alert_bound),
			'alert_sms_type' => $alert_sms_type
		);

		return trim($this->_httpRequest($domain, $path, $port, $data));
	}

	/*
	 * Fonction credit_sub_account
	 * 
	 * Make the request = get the token + use the token into a request from server to server to execute the transfer
	 * 
	 */

	public function credit_sub_account($user_login, $api_key, $sub_account_email, $sms_amount, $sms_type)
	{
		$domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_CREDIT_SUB_ACCOUNT_TOKEN;
		$port	 = inSIM_PORT;

		$data = array(
			'user_login'		 => $user_login,
			'api_key'			 => $api_key,
			'sub_account_email'	 => $sub_account_email
		);

		$xml_return	 = trim($this->_httpRequest($domain, $path, $port, $data));
		/* 	 $xml_return = '<?xml version="1.0" encoding="UTF-8"?>
		  <octopush>
		  <token>F76C90C4F269289575363AE34BF6E399</token>
		  </octopush>'; */
		libxml_use_internal_errors(true);
		if (($xml		 = simplexml_load_string($xml_return)) !== false)
		{
			$res = (array) $xml->xpath('/insim');
			$tt	 = ((array) $res[0]);
			if (isset($tt['error_code']) && $tt['error_code'] !== '000')
				return 'An error ' . $tt['error_code'] . ' has occured.';

			if (!isset($tt['token']))
				return 'An error has occured.';
			$token = $tt['token'];
		}
		else
		{
			return 'An error has occured.';
		}

		/* Now, the token is ready, we can ask for the transfer to be done */

		$domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_CREDIT_SUB_ACCOUNT;
		$port	 = inSIM_PORT;

		/* We check that the type of the sms belong to the usual values */
		if ($sms_type != 'FR' && $sms_type != 'XXX')
		{
			$sms_type = 'FR';
		}

		$data = array(
			'user_login'		 => $user_login,
			'api_key'			 => $api_key,
			'sub_account_email'	 => $sub_account_email,
			'sms_number'		 => $sms_amount,
			'sms_type'			 => $sms_type,
			'token'				 => $token
		);

		$xml_return = trim($this->_httpRequest($domain, $path, $port, $data));
		return $xml_return;
	}

	public function getBalance()
	{
		$domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_BALANCE;
		$port	 = inSIM_PORT;

		$data = array(
			'user_login' => $this->user_login,
			'api_key'	 => $this->api_key
		);

		return trim($this->_httpRequest($domain, $path, $port, $data));
	}
        
        public function set_user_lang($lang) {
            $this->user_lang = $lang;            
        }
        
        public function getNews()
        {
            $domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_NEWS;
		$port	 = inSIM_PORT;

		$data = array(
			'lang' => $this->user_lang,
			'channel' => CHANNEL
		);

		return trim($this->_httpRequest($domain, $path, $port, $data));
        
        }

	private function _get_request_sha1_string($request_keys, $data)
	{
		$A_char_to_field = array(
			'T'	 => 'sms_text',
			'R'	 => 'sms_recipients',
			'M'	 => 'sms_mode',
			'Y'	 => 'sms_type',
			'S'	 => 'sms_sender',
			'D'	 => 'sms_date',
			'a'	 => 'recipients_first_names',
			'b'	 => 'recipients_last_names',
			'c'	 => 'sms_fields_1',
			'd'	 => 'sms_fields_2',
			'e'	 => 'sms_fields_3',
			'W'	 => 'with_replies',
			'N'	 => 'transactional',
			'Q'	 => 'request_id'
		);
		$request_string	 = '';
		for ($i = 0, $n = strlen($this->request_keys); $i < $n; $i++)
		{
			$char = $this->request_keys[$i];

			if (!isset($A_char_to_field[$char]) || !isset($data[$A_char_to_field[$char]]))
				continue;
			$request_string .= $data[$A_char_to_field[$char]];
		}
		$request_sha1 = sha1($request_string);
		return $request_sha1;
	}

	private function _httpRequest($domain, $path, $port, $A_fields = array())
	{
		return true;
	}

	public function set_user_login($user_login)
	{
		$this->user_login = $user_login;
	}

	public function set_api_key($api_key)
	{
		$this->api_key = $api_key;
	}

	public function set_sms_text($sms_text)
	{
		$this->sms_text = $sms_text;
	}

	public function set_sms_type($sms_type)
	{
		$this->sms_type = $sms_type;
	}

	public function set_sms_recipients($sms_recipients)
	{
		$this->sms_recipients = $sms_recipients;
	}

	public function set_recipients_first_names($first_names)
	{
		$this->recipients_first_names = $first_names;
	}

	public function set_recipients_last_names($last_names)
	{
		$this->recipients_last_names = $last_names;
	}

	public function set_sms_fields_1($sms_fields_1)
	{
		$this->sms_fields_1 = $sms_fields_1;
	}

	public function set_sms_fields_2($sms_fields_2)
	{
		$this->sms_fields_2 = $sms_fields_2;
	}

	public function set_sms_fields_3($sms_fields_3)
	{
		$this->sms_fields_3 = $sms_fields_3;
	}

	public function set_sms_mode($sms_mode)
	{
		$this->sms_mode = $sms_mode;
	}

	public function set_sms_sender($sms_sender)
	{
		$this->sms_sender = $sms_sender;
	}

	public function set_date($y, $m, $d, $h, $i)
	{
		$this->sms_y = intval($y);
		$this->sms_d = intval($d);
		$this->sms_m = intval($m);
		$this->sms_h = intval($h);
		$this->sms_i = intval($i);
                
		$this->sending_date = mktime($this->sms_h, $this->sms_i, 0, $this->sms_m, $this->sms_d);
	}

	public function set_simulation_mode()
	{
		$this->request_mode = inSIM_SIMULATION;
	}

	public function set_sms_ticket($sms_ticket)
	{
		$this->sms_ticket = $sms_ticket;
	}

	public function set_sms_request_id($request_id)
	{
		$this->request_id = preg_replace('`[^0-9a-zA-Z]*`', '', $request_id);
	}

	/*
	 * Notify Octopush plateform that you want to recieve the answers that your recipients will send back to your sending(s) 
	 */

	public function set_option_with_replies($with_replies)
	{
		if (!isset($with_replies) || intval($with_replies) !== 1)
		{
			$this->with_replies = 0;
		}
		else
		{
			$this->with_replies = 1;
		}
	}

	/*
	 * Notify Octopush that you are making a transactional sending. 
	 * With this option, sending marketing SMS is strongly forbidden, and may make your account blocked in case of abuses.
	 * DO NOT USE this option if you are not sure to understand what a transactional SMS is.
	 */

	public function set_option_transactional($transactional)
	{
		if (!isset($transactional) || intval($transactional) !== 1)
		{
			$this->transactional = 0;
		}
		else
		{
			$this->transactional = 1;
		}
	}

	/*
	 * Use a MSISDN number.
	 */

	public function set_sender_is_msisdn($msisdn_sender)
	{
		$this->msisdn_sender = $msisdn_sender;
	}

	public function set_request_keys($request_keys)
	{
		$this->request_keys = $request_keys;
	}

	public function set_user_batch_id($user_batch_id)
	{
		$this->user_batch_id = preg_replace('`[^0-9a-zA-Z]*`', '', $user_batch_id);
	}

	/*
	 * Finish the transmission of trames and allow platform to build campaign from package.
	 */

	public function set_finished($finished)
	{
		if (intval($finished) === 1)
			$this->finished	 = 1;
		else
			$this->finished	 = 0;
	}

	/*
	 * get the "status", "send" or "delete" a campaign.
	 */

	public function set_action($action)
	{
		$this->action = $action;
	}

}

?>
