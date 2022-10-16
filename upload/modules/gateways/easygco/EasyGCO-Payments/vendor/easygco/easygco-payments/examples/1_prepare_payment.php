<?php
include(__DIR__ . './../vendor/autoload.php');

/*
    The below example intiates a new Payment URL
*/

$apiKey = 'YOUR_API_KEY';
$apiSecret = 'YOUR_API_SECRET';


$ePaymentsClient = new EasyGCO\EasyGCOPayments\API($apiKey,$apiSecret);

$testApiPath = 'payment/prepare';

$testInputData = [
    'transaction_id' => 'test-txn-id-1',                                            // Transaction ID on your Script / Application
    'description' => 'My First Invoice',                                            // Transaction Description
    'code' => 'USD',                                                                // Currency 3-Letter Code as [ # ISO 4217 ]
    'type' => 'fiat',                                                               // Currency Type [fiat , asset]
    'amount' => 10.00,                                                              // Amount based on provided currency
    'return_url' => 'https://www.example.com/return/myinvoice/',                    // Return URL - in case payment is failed
    'success_url' => 'https://www.example.com/return/myinvoice/payment-success/',   // Success URL - in case payment has been completed successfully
    'cancel_url' => 'https://www.example.com/return/myinvoice/payment-cancelled/',  // Cancel URL - in case payment has been canceled
];

$apiResponse = $ePaymentsClient->doRequest($testApiPath, $testInputData);

if(!$ePaymentsClient->isSuccess($apiResponse)) 
    exit($ePaymentsClient->getMessage($apiResponse));

$responseData = $ePaymentsClient->getData($apiResponse);

$paymentUID = $responseData['ePaymentUid'];  // Payment UID can be used to check this payment later, and will be automatically appended to your provided URLs

$paymentURL = "https://easygco.com/payment/?uid=$paymentUID";

echo "Payment Request has been successfully generated:\r\n";
echo $paymentURL;
exit();
