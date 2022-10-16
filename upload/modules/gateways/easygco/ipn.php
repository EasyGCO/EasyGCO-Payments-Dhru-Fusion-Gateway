<?php
/*
 * EasyGCO Payments Gateway for Dhru Fusion
 *
 * @copyright Copyright (c) EasyGCO.com
 * 
 * @author   EasyGCO ( easygco.com )
 * @version  1.0.0
 */

if(empty($_REQUEST) || empty($_REQUEST['ps_response_data']) || !is_array($_REQUEST['ps_response_data'])) {
    http_response_code(403);
    exit('Error: Access is denied');
}

$apiResponseData = $_REQUEST['ps_response_data'];

if(!isset($apiResponseData['payment_uid'])) {
    http_response_code(403);
    exit('Error: Invalid PS-Data, no payment UID identified, Access is denied');
}

if(empty($apiResponseData['externalid'])) {
    http_response_code(403);
    exit('Success: IPN Received, payment transaction reference empty or not provided');
}

define("DEFINE_MY_ACCESS", true);
define("DEFINE_DHRU_FILE", true);

include '../../../comm.php';
require '../../../includes/fun.inc.php';
include '../../../includes/gateway.fun.php';
include '../../../includes/invoice.fun.php';

unset($invoiceid,$txn_type,$userid);

$paymentGateway = loadGatewayModule('easygco');

$apiKey = $paymentGateway['api_key'];
$apiSecret = $paymentGateway['api_secret'];

require_once(__DIR__ . '/EasyGCO-Payments/vendor/autoload.php');

$ePaymentsClient = new \EasyGCO\EasyGCOPayments\API($apiKey,$apiSecret);

$paymentUID = $apiResponseData['payment_uid'];
$invoiceUID = (int) filter_var($apiResponseData['externalid'], FILTER_SANITIZE_NUMBER_INT);

$apiPath = 'payment/get';

$inputData = [
    'uid' => trim(urldecode($paymentUID)),
];

$apiResponse = $ePaymentsClient->doRequest($apiPath, $inputData);

if(!$ePaymentsClient->isSuccess($apiResponse)) {
    http_response_code(200);
    exit('Failed: IPN Received, No action taken, cannot verify payment UID');
}

$responseData = $ePaymentsClient->getData($apiResponse);

if(!isset($responseData['success']) || intval($responseData['success']) !== 1) {
    http_response_code(200);
    exit('Failed: IPN Received, No action taken, payment is unsuccessful');
}

$paidAmount = (float) number_format($responseData['input_amounts']['paid'], 4, '.', '');

logTransaction('paypal', $responseData, 'Successful','invoice',$invoiceUID);
addPayment($invoiceUID, $paymentUID, $paidAmount, 0, 'easygco');

