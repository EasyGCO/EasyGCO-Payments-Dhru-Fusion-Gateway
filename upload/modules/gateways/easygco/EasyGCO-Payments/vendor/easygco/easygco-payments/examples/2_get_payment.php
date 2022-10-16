<?php
include(__DIR__ . './../vendor/autoload.php');

/*
    Get payment status after payment completion or failure
*/

$apiKey = 'YOUR_API_KEY';
$apiSecret = 'YOUR_API_SECRET';

$paymentUID = 'PAYMENT_UID';


$ePaymentsClient = new EasyGCO\EasyGCOPayments\API($apiKey,$apiSecret);

$testApiPath = 'payment/get';

$testInputData = [
    'uid' => $paymentUID,
];

$apiResponse = $ePaymentsClient->doRequest($testApiPath, $testInputData);

if(!$ePaymentsClient->isSuccess($apiResponse)) 
    exit($ePaymentsClient->getMessage($apiResponse));

var_dump($ePaymentsClient->getData($apiResponse));
exit();