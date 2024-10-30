<?php

/**
 * Librairie de services complémentaires via POST HTTP
 *
 * Auteur Yoni Guimberteau yoni@octopush.com
 *
 * copyright (c) 2013 Yoni Guimberteau
 * licence : utilisation, modification, commercialisation.
 * L'auteur ainsi se decharge de toute responsabilite
 * concernant une quelconque utilisation de ce code, livre sans aucune garantie.
 * Il n'est distribue qu'a titre d'exemple de fonctionnement du module POST HTTP de OCTOPUSH,
 * Vous pourrez toutefois telecharger une version actualisee sur www.octopush.com
 */
include_once ('config.inc.php');

class inSIM_OWS
{

	public $user_login; // string
	public $api_key;   // string
	public $answer_email; // string
	public $sms_alert_bound; // int
	public $sms_alert_type; // FR ou XXX

	public function __construct()
	{
		$this->user_login	 = '';
		$this->api_key		 = '';

		$this->answer_email		 = -1;
		$this->sms_alert_bound	 = -1;
		$this->sms_alert_type	 = -1;
	}

	/*
	 * Fonction credit_sub_account
	 * 
	 * Make the request -> token -> request from server to server to execute the transfer
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
			$res	 = (array) $xml->xpath('/octopush');
			$tt		 = ((array) $res[0]);
			$token	 = $tt['token'];
		}
		else
		{
			return 'An error as occured.';
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

	public function edit_options()
	{
		$domain	 = inSIM_DOMAIN;
		$path	 = inSIM_PATH_EDIT_OPTIONS;
		$port	 = inSIM_PORT;

		$data = array(
			'user_login' => $this->user_login,
			'api_key'	 => $this->api_key);

		if ($this->answer_email !== -1)
			$data['answer_email']	 = $this->answer_email;
		if ($this->sms_alert_bound !== -1)
			$data['sms_alert_bound'] = $this->sms_alert_bound;
		if ($this->sms_alert_type !== -1)
			$data['sms_alert_type']	 = $this->sms_alert_type;

		$xml_return = trim($this->_httpRequest($domain, $path, $port, $data));
		return $xml_return;
	}

	public function get_balance()
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

	public function set_answer_email($answer_email)
	{
		$this->answer_email = $answer_email;
	}

	public function set_sms_alert_bound($sms_alert_bound)
	{
		$this->sms_alert_bound = intval($sms_alert_bound);
	}

	public function set_sms_alert_type($sms_alert_type)
	{
		if (in_array($sms_alert_type, array(inSIM_SMS_PREMIUM, inSIM_SMS_STANDARD)))
			$this->sms_alert_type = $sms_alert_type;
	}

	/*
	 * XML PARSING FUNCTIONS
	 */

	public function parse_octopush_token_result($xml)
	{
		libxml_use_internal_errors(true);
		if (($xml = simplexml_load_string($xml)) !== false)
		{
			$result	 = $xml->xpath('/octopush');
			$res	 = (array) $result[0];
			$token	 = $res['token'];
		}
		else
		{
			$token = '';
		}

		return $token;
	}

}

?>
