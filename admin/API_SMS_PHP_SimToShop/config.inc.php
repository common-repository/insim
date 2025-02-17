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

//send one sms https://www.octopush-dm.com/api_wooc_os/sms_send
//send trames https://www.octopush-dm.com/api_wooc_os/sms_parts
//statut delete send https://www.octopush-dm.com/api_wooc_os/sms_batch

define('inSIM_DOMAIN', 'https://www.ardary-sms.com/');
define('inSIM_CHANNEL','woo');
define('inSIM_PORT', '80');
define('inSIM_PATH', '');
define('inSIM_PATH_BIS', '');
$path = inSIM_PATH;

define('inSIM_PATH_SMS', $path . '/api');
define('inSIM_PATH_BALANCE', $path . '/api/balance');
define('inSIM_PATH_NEWS', $path . '/api/news');

// Options ouvertes sur demande. Demandez la documention annexe pour utiliser ces webservices.
define('inSIM_PATH_SUB_ACCOUNT', $path . '/api_sub/add_sub_account');
define('inSIM_PATH_CREDIT_SUB_ACCOUNT_TOKEN', $path . '/api_sub/credit_sub_account_get_session');
define('inSIM_PATH_CREDIT_SUB_ACCOUNT', $path . '/api_sub/credit_sub_account');
define('inSIM_PATH_OSTP', $path . '/api/open_single_temp_session');
define('inSIM_PATH_EDIT_OPTIONS', $path . '/api/edit_options');
define('inSIM_PATH_GET_USER_INFO', $path . '/api_sub/get_user_info');

define('inSIM_PATH_SMS_PARTS', $path . '/api_wooc_os/sms_parts');
define('inSIM_PATH_BATCH', $path . '/api_wooc_os/sms_batch');

define('inSIM_CUT_', 7);

define('inSIM_SMS_STANDARD', 'XXX');
define('inSIM_SMS_WORLD', 'WWW');
define('inSIM_SMS_PREMIUM', 'FR');

define('inSIM_INSTANTANE', 1);
define('inSIM_DIFFERE', 2);

define('inSIM_SIMULATION', 'simu');
define('inSIM_REEL', 'real');

define('inSIM_STR_STOP_',		'STOP au XXXXX');

//error
define('inSIM_ERROR_OK_',                                'OK');
define('inSIM_ERROR_KO_',                                'KO');
define('inSIM_ERROR_CODE_OK_',                            0);
define('inSIM_ERROR_CODE_KO_',                            500);
define('inSIM_ERROR_MISSING_POST_PARAMETERS_',            100);
define('inSIM_ERROR_WRONG_IDENTIFIERS_',101);
define('inSIM_ERROR_SMS_TOO_LONG_',102);
define('inSIM_ERROR_NO_RECIPIENTS_',                        103);
define('inSIM_ERROR_LOW_MONEY_',                            104);
define('inSIM_ERROR_LOW_MONEY_BUT_ORDER_',                105);
define('inSIM_ERROR_BAD_SENDER_',                        106);
define('inSIM_ERROR_MISSING_PARAMETER_TEXT_',            107);
define('inSIM_ERROR_MISSING_PARAMETER_LOGIN_',            108);
define('inSIM_ERROR_MISSING_PARAMETER_PASSWORD_',        109);
define('inSIM_ERROR_MISSING_PARAMETER_RECIPIENTS_',        110);
define('inSIM_ERROR_MISSING_PARAMETER_DEST_CHOSE_WAY_',  111);
define('inSIM_ERROR_MISSING_PARAMETER_QUALITY_',            112);
define('inSIM_ERROR_UNCHECKED_PHONE_',                    113);
define('inSIM_ERROR_BLACKLISTED_USER_',                    114);
define('inSIM_ERROR_RECIPIENTS_PARAMS_NUMBER_',            115);
define('inSIM_ERROR_MAILING_UNAVAILABLE_',                116);
define('inSIM_ERROR_RECIPIENTS_WRONG_FORMAT_',            117);
define('inSIM_ERROR_NOT_ANY_CHECKED_CHECKBOX_',            118);
define('inSIM_ERROR_LONG_LOWCOST_SMS_NOT_ALLOWED_',        119);
define('inSIM_ERROR_REQUEST_ID_ALREADY_EXISTS_',            120);
define('inSIM_ERROR_STOP_MENTION_IS_MISSING_',            121);
define('inSIM_ERROR_NO_PUB_MENTION_IS_MISSING_',            122);
define('inSIM_ERROR_STRING_SHA1_IS_MISSING_',            123);
define('inSIM_ERROR_STRING_SHA1_IS_WRONG_',                124);
define('inSIM_ERROR_AN_UNKNOWN_ERROR_HAS_OCCURED_',        125);
define('inSIM_ERROR_HUGE_CAMPAIGN_ALREADY_AWAITING_',    126);
define('inSIM_ERROR_HUGE_CAMPAIGN_BEING_COMPUTED_',        127);
define('inSIM_ERROR_TOO_MUCH_SUBMITIONS_',                128);
define('inSIM_ERROR_BATCH_SMS_PROSSESSING_',                129);
define('inSIM_ERROR_BATCH_SMS_NOT_FINISHED_',            130);
define('inSIM_ERROR_BATCH_SMS_NOT_FOUND_',                131);
define('inSIM_ERROR_BATCH_SMS_ALREADY_SENT_',            132);

define('inSIM_ERROR_UNFOUND_COUNTRY_CODE_',                150);
define('inSIM_ERROR_COUNTRY_UNAVAILABLE_',                151);
define('inSIM_ERROR_COUNTRY_NOT_AVAILABLE_IN_LC_',        152);
define('inSIM_ERROR_ROUTE_NOT_UNAVAILABLE_LC_',            153);

define('inSIM_ERROR_U_R_NOT_ALLOWED_SUB_USERS_',            201);
define('inSIM_ERROR_SUB_USER_WRONG_EMAIL_',                202);
define('inSIM_ERROR_SUB_USER_TOO_MUCH_TOKENS_',            203);
define('inSIM_ERROR_SUB_USER_WRONG_TOKEN_',                204);
define('inSIM_ERROR_SUB_USER_NB_SMS_TOO_LOW_',            205);
define('inSIM_ERROR_SUB_USER_CANT_TRANSFER_CAMPAIGN_',    206);
define('inSIM_ERROR_SUB_USER_CANT_ACCESS_',                207);
define('inSIM_ERROR_SUB_USER_WRONG_SMS_TYPE_',            208);
define('inSIM_ERROR_SUB_USER_FORBIDDEN_',                209);
define('inSIM_ERROR_EMAIL_DOES_NOT_BELONG_TO_YOURS_',    210);

define('inSIM_ERROR_U_R_NOT_ALLOWED_LISTS_',                300);
define('inSIM_ERROR_MAX_NUMBER_OF_LISTS_REACHED_',        301);
define('inSIM_ERROR_A_LIST_ALLREADY_EXISTS_',            302);
define('inSIM_ERROR_LIST_DOESNT_EXIST_',                    303);
define('inSIM_ERROR_LIST_IS_FULL_',                        304);
define('inSIM_ERROR_TO_MUCH_CONTACTS_IN_REQUEST_',        305);
define('inSIM_ERROR_LIST_ACTION_UNKNOWN_',                306);

$GLOBALS['errors'] = array(
    inSIM_ERROR_OK_ => __('OK','octopush-sms'),
    inSIM_ERROR_KO_ => __('KO','octopush-sms'),
    inSIM_ERROR_CODE_OK_ => __('OK','octopush-sms'),
    inSIM_ERROR_CODE_KO_ => __('Impossible to process requested action','octopush-sms'),
    inSIM_ERROR_MISSING_POST_PARAMETERS_ => __('POST request missing.','octopush-sms'),
    inSIM_ERROR_WRONG_IDENTIFIERS_ => __('Incorrect login details.','octopush-sms'),
    inSIM_ERROR_SMS_TOO_LONG_ => __('Your SMS exceeds 160 characters','octopush-sms'),
    inSIM_ERROR_NO_RECIPIENTS_ => __('Your message has no recipient(s)','octopush-sms'),
    inSIM_ERROR_LOW_MONEY_ => __('You have run out of credit.','octopush-sms'),
    inSIM_ERROR_LOW_MONEY_BUT_ORDER_ => __('You don\'t have enough credit on your balance your last order is being processed','octopush-sms'),
    inSIM_ERROR_BAD_SENDER_ => __('You have entered the sender ID incorrectly. 3 to 11 characters, chosen from 0 to 9, a to z, A to Z. No accent, space or punctuation.','octopush-sms'),
    inSIM_ERROR_MISSING_PARAMETER_TEXT_ => __('The text of your message is missing.','octopush-sms'),
    inSIM_ERROR_MISSING_PARAMETER_QUALITY_ => __('You have not defined the quality of the route for your message.','octopush-sms'),
    inSIM_ERROR_MISSING_PARAMETER_LOGIN_ => __('You have not entered your login details.','octopush-sms'),
    inSIM_ERROR_MISSING_PARAMETER_PASSWORD_ => __('You have not entered your password.','octopush-sms'),
    inSIM_ERROR_MISSING_PARAMETER_RECIPIENTS_ => __('You have not entered the list of recipients.','octopush-sms'),
    inSIM_ERROR_MISSING_PARAMETER_DEST_CHOSE_WAY_ => __('You have not chosen a way to enter your recipients.','octopush-sms'),
    inSIM_ERROR_RECIPIENTS_PARAMS_NUMBER_ => __('Your recipient list does not contain a valid number.','octopush-sms'),
    inSIM_ERROR_UNCHECKED_PHONE_ => __('Your account is not validated. Log in and go to the "User interface" section.','octopush-sms'),
            'error_web_' . inSIM_ERROR_UNCHECKED_PHONE_ => __('Your account is not validated. <br /><br /> Click on the button below to activate it.','octopush-sms'),
            inSIM_ERROR_BLACKLISTED_USER_ => __('You are under investigation for the fraudulent use of our services.','octopush-sms'),
            inSIM_ERROR_RECIPIENTS_PARAMS_NUMBER_ => __('The recipient number is different from the number defined in the parameters\' section related to it.','octopush-sms'),
            inSIM_ERROR_MAILING_UNAVAILABLE_ => __('The bulk mailing option only works by using a contact list.','octopush-sms'),
            inSIM_ERROR_RECIPIENTS_WRONG_FORMAT_ => __('Your recipient list does not contain any correct number. Have you formated your numbers by including the international dialling code? Contact us if you have any problems.','octopush-sms'),
            inSIM_ERROR_NOT_ANY_CHECKED_CHECKBOX_ => __('You must tick one of the two boxes to indicate if <u>you do not wish to send test SMS </u> or if <u>you have correctly received and validated your test SMS</u>.','octopush-sms'),
            inSIM_ERROR_LONG_LOWCOST_SMS_NOT_ALLOWED_ => __('You cannot send SMS with more than x characters for this type of SMS','octopush-sms'),
            inSIM_ERROR_REQUEST_ID_ALREADY_EXISTS_ => __('A SMS with the same request_id has already been sent.','octopush-sms'),
            inSIM_ERROR_STOP_MENTION_IS_MISSING_ => __('In Premium SMS, the mention "'.inSIM_STR_STOP_.'" is mandatory and must belong to your text (respect the case).','octopush-sms'),
            inSIM_ERROR_NO_PUB_MENTION_IS_MISSING_ => __('In Standard SMS, the mention _STR_NO_PUB_  is mandatory  and must belong to your text (respect the case).','octopush-sms'),
            inSIM_ERROR_STRING_SHA1_IS_MISSING_ => __('The field request_sha1 is missing.','octopush-sms'),
            inSIM_ERROR_STRING_SHA1_IS_WRONG_ => __('The field request_sha1 does not match. The data is wrong, or the query string contains an error or the frame contains an error : the request is rejected.','octopush-sms'),
            inSIM_ERROR_AN_UNKNOWN_ERROR_HAS_OCCURED_ => __('An undefined error has occurred. Please contact support.','octopush-sms'),
            inSIM_ERROR_HUGE_CAMPAIGN_ALREADY_AWAITING_ => __('An SMS campaign is already waiting for approval. You must validate or Cancel it in order to to start another.','octopush-sms'),
            inSIM_ERROR_HUGE_CAMPAIGN_BEING_COMPUTED_ => __('A SMS campaign is already being processed. You must wait for processing to be completed in order to start another one.','octopush-sms'),
            inSIM_ERROR_TOO_MUCH_SUBMITIONS_ => __('Too many attempts have been made. You need to start a new campaign.','octopush-sms'),
            inSIM_ERROR_BATCH_SMS_PROSSESSING_ => __('Campaign is being built.','octopush-sms'),
            inSIM_ERROR_BATCH_SMS_NOT_FINISHED_ => __('Campaign is not yet finished.','octopush-sms'),
            inSIM_ERROR_BATCH_SMS_NOT_FOUND_ => __('Campaign not found.','octopush-sms'),
            inSIM_ERROR_BATCH_SMS_ALREADY_SENT_ => __('Campaign sent.','octopush-sms'),
            inSIM_ERROR_UNFOUND_COUNTRY_CODE_ => __('No country was found for this country.','octopush-sms'),
            inSIM_ERROR_COUNTRY_UNAVAILABLE_ => __('The recipient country code is not part of our country coverage.','octopush-sms'),
            inSIM_ERROR_COUNTRY_NOT_AVAILABLE_IN_LC_ => __('You cannot send low cost SMS to this country. Choose Premium SMS','octopush-sms'),
            inSIM_ERROR_ROUTE_NOT_UNAVAILABLE_LC_ => __('The route is congested. This type of SMS cannot be dispatched immediately. If your order is urgent, please use another type of SMS.','octopush-sms'),
            inSIM_ERROR_U_R_NOT_ALLOWED_SUB_USERS_ => __('This option is only available upon request. Do not hesitate to contact us if you want to activate it','octopush-sms'),
            inSIM_ERROR_SUB_USER_WRONG_EMAIL_ => __('The email account you wish to credit is incorrect.','octopush-sms'),
            inSIM_ERROR_SUB_USER_TOO_MUCH_TOKENS_ => __('You already have a token in use. You can only have one session opened at a time.','octopush-sms'),
            inSIM_ERROR_SUB_USER_WRONG_TOKEN_ => __('You have specified a wrong token.','octopush-sms'),
            inSIM_ERROR_SUB_USER_NB_SMS_TOO_LOW_ => __('The number of text messages you want to transfer is too low.','octopush-sms'),
            inSIM_ERROR_SUB_USER_CANT_TRANSFER_CAMPAIGN_ => __('You may not run any campaign if your account is not refilled.','octopush-sms'),
            inSIM_ERROR_SUB_USER_CANT_ACCESS_ => __('You do not have access to this feature.','octopush-sms'),
            inSIM_ERROR_SUB_USER_WRONG_SMS_TYPE_ => __('Wrong type of SMS.','octopush-sms'),
            inSIM_ERROR_SUB_USER_FORBIDDEN_ => __('You are not allowed to send SMS messages to this user.','octopush-sms'),
            inSIM_ERROR_EMAIL_DOES_NOT_BELONG_TO_YOURS_ => __('This email is not specified in any of your sub accounts or affiliate users.','octopush-sms'),
            inSIM_ERROR_U_R_NOT_ALLOWED_LISTS_ => __('You are not authorized to manage your lists by API.','octopush-sms'),
            inSIM_ERROR_MAX_NUMBER_OF_LISTS_REACHED_ => __('You have reached the maximum number of contact lists.','octopush-sms'),
            inSIM_ERROR_A_LIST_ALLREADY_EXISTS_ => __('A list with the same name already exists.','octopush-sms'),
            inSIM_ERROR_LIST_DOESNT_EXIST_ => __('The specified list does not exist.','octopush-sms'),
            inSIM_ERROR_LIST_IS_FULL_ => __('The list is already full.','octopush-sms'),
            inSIM_ERROR_TO_MUCH_CONTACTS_IN_REQUEST_ => __('There are too many contacts in the query.','octopush-sms'),
            inSIM_ERROR_LIST_ACTION_UNKNOWN_ => __('The requested action is unknown.','octopush-sms'));
?>