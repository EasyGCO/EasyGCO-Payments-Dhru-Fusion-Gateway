<?php
/*
 * EasyGCO Payments Gateway for Dhru Fusion
 *
 * @copyright Copyright (c) EasyGCO.com
 * 
 * @author   EasyGCO ( easygco.com )
 * @version  1.0.0
 */

defined("DEFINE_MY_ACCESS") or die('<h1 style="color: #C00; text-align: center;"><strong>Restricted Access</strong></h1>');

function easygco_config() {
    $configarray = array(
        'name' => array('Type' => 'System', 'Value' => 'EasyGco'),
        'api_key' => array(
            'Name' => 'API Key',
            'Type' => 'text',
            'Size' => '40',
            'Description' => 'EasyGco Payments API Key'),
		'api_secret' => array(
			'Name' => 'API Secret',
			'Type' => 'text',
			'Size' => '40',
			'Description' => 'EasyGco Payments API Secret'),
            );
    return $configarray;
}

function easygco_link($params) {
    
    global $config;
    global $lng_languag;


	if(empty($params)) return null;

    $invoicetotal = $params['amount'];

	$failURL = $params['returnurl'] . '&paymentfailed=true';
	$successURL = $params['returnurl'];
	$returnURL = $params['returnurl'];

	$ipnURL = $params['systemurl'] . '/modules/gateways/easygco/ipn.php';

	$apiKey = $params['api_key'];
	$apiSecret = $params['api_secret'];

	require_once(__DIR__ . '/easygco/EasyGCO-Payments/vendor/autoload.php');

	if(empty($apiKey) || empty($apiSecret) || !is_string($apiKey) || !is_string($apiSecret)) return null;
	
	$ePaymentsClient = new \EasyGCO\EasyGCOPayments\API($apiKey,$apiSecret);

	$apiPath = 'token/generate';

	$inputData = [
		'transaction_id' 	=> $params['invoiceid'],
		'description' 		=> $params['description'],
		'code' 				=> $params['currency'],
		'type' 				=> 'fiat_money',
		'amount' 			=> 	number_format($invoicetotal, 2, '.', ''),
		"return_url"		=>	$failURL,
		"success_url"		=>	$successURL,
		"cancel_url"		=>	$returnURL,
		"notify_url"		=>	$ipnURL,
	];

	$apiResponse = $ePaymentsClient->doRequest($apiPath, $inputData);
	if(!$ePaymentsClient->isSuccess($apiResponse)) return null;

	$responseData = $ePaymentsClient->getData($apiResponse);
	if(empty($responseData['url'])) return null;
	return  '<a class="btn btn-block btn-lg btn-success px-2 py-3" target="_parent" href="' . $responseData['url'] . '">'.$lng_languag['invoicespaynow'].'</a>';

}
