# payment_php
Interswitch Payment PHP Library


## Installation

Install the latest version with

```bash
$ composer require Interswitch\Payment
```

## Basic Usage

```php
<?php

require_once __DIR__.'/vendor/autoload.php';
use Interswitch\Payment as Payment;

// Initialize Interswitch object
$CLIENT_ID = "IKIA9614B82064D632E9B6418DF358A6A4AEA84D7218";
$CLIENT_SECRET = "XCTiBtLy1G9chAnyg0z3BcaFK4cVpwDg/GTw2EmjTZ8=";
$env = Payment::ENV_SANDBOX; // Payment::ENV_PRODUCTION
$payment = new Payment($CLIENT_ID, $CLIENT_SECRET, $env);

$pan = '6280511000000095';
$expDate = '5004';
$cvv = '111';
$pin = '1111';
$customerId = "api-jam@interswitchgroup.com";
$currency = "NGN";
$amt = "10000";
$otpPan = "5061020000000000011";
$otpExpDate = "1801";
$otpCvv = "350";
$otpPin = "1111";

// Validate Card
$validateReqRef = "ISW|API|JAM|" . mt_rand(0, 65535); // generate a random ref
$validateResp = $payment->validateCard($pan, $expDate, $cvv, $pin, $validateReqRef);
$httpResp = $validateResp['HTTP_CODE'];
$respBody = $validateResp['RESPONSE_BODY'];
$json_resp = json_decode($respBody);

// Make Payment
$paymentReqRef = "ISW|API|JAM|" . mt_rand(0, 65535); // generate a random ref
$paymentResp = $payment->authorize($pan, $expDate, $cvv, $pin, $amt, $currency, $customerId, $paymentReqRef);
$httpResp = $paymentResp['HTTP_CODE'];
$respBody = $paymentResp['RESPONSE_BODY'];
$json_resp = json_decode($respBody);

// Get Status
$statusResp = $payment->getStatus($paymentReqRef, $amt);
$httpResp = $statusResp['HTTP_CODE'];
$respBody = $statusResp['RESPONSE_BODY'];
$json_resp = json_decode($respBody);


// Validate OTP Card
$validateReqRef = "ISW|API|JAM|" . mt_rand(0, 65535); // generate a random ref
$validateResp = $payment->validateCard($otpPan, $otpExpDate, $otpCvv, $otpPin, $validateReqRef);
$httpRespCode = $validateResp["HTTP_CODE"];
$respBody = $validateResp["RESPONSE_BODY"];

// Verify OTP - Validate Card
if($httpRespCode == '200' || $httpRespCode == '201' || $httpRespCode == '202')
{
  $json_resp = json_decode($respBody);
  $tranId = $json_resp->{'transactionRef'};
  $otp = "123456"; // Get this from SMS
  $verifyOTPResp = $payment->verifyOTP($tranId, $otp);
  $httpRespCode = $verifyOTPResp["HTTP_CODE"];
  $respBody = $verifyOTPResp["RESPONSE_BODY"];
}

// Make OTP Payment
$paymentReqRef = generateRef();
$paymentResp = $payment->authorize($otpPan, $otpExpDate, $otpCvv, $otpPin, $amt, $currency, $customerId, $paymentReqRef);
$httpRespCode = $paymentResp["HTTP_CODE"];
$respBody = $paymentResp["RESPONSE_BODY"];

// Verify OTP - Payment
if($httpRespCode == '200' || $httpRespCode == '201' || $httpRespCode == '202')
{
  $jsonObj = json_decode($respBody);
  $tranId = $jsonObj->{'paymentId'};
  $otp = "123456";
  $verifyOTPResp = $payment->verifyOTP($tranId, $otp);
  $httpRespCode = $verifyOTPResp["HTTP_CODE"];
  $respBody = $verifyOTPResp["RESPONSE_BODY"];
}

```


## Third Party Packages

- interswitch
- JWT

## About

### Requirements

- Intersiwtch Payment SDK works with PHP 5.0 or above.

### Author

Lekan Omotayo - <developer@interswitchgroup.com><br />
Abiola Adebanjo - <developer@interswitchgroup.com><br />

### License

Payment SDK is licensed under the ISC License



