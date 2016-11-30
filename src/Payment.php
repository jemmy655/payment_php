<?php
/**
 * Description of PaymentGateway
 *
 * @author Lekan.Omotayo
 */

namespace Interswitch;

require_once __DIR__.'/lib/Interswitch.php';
use Interswitch\Interswitch as Interswitch;

class Payment {

private $clientId;
private $clientSecret;
private $environment;
private $interswitch;

const HTTP_CODE = "HTTP_CODE";
const RESPONSE_BODY = "RESPONSE_BODY";
const ENV_PRODUCTION = Interswitch::ENV_PRODUCTION;
const ENV_SANDBOX = Interswitch::ENV_SANDBOX;


public function __construct($clientId, $clientSecret, $environment = null) {
  $this->clientId = $clientId;
  $this->clientSecret = $clientSecret;
  if ($environment !== null) {
    $this->environment = $environment;
  }
  $this->interswitch = new Interswitch($this->clientId, $this->clientSecret, $this->environment);
}

function authorize($pan, $expDate, $cvv = null, $pin = null, $amt, $currency, $customerId, $reqRef)
{
  $authData = $this->interswitch->getAuthData($pan, $expDate, $cvv, $pin);
  $paymentReq = array(
   "customerId" => $customerId,
   "amount" => $amt,
   "transactionRef" => $reqRef,
   "currency" => $currency,
   "authData" => $authData
  );
  $paymentJsonReq = json_encode($paymentReq);
  $paymentResp = $this->interswitch->send('api/v2/purchases', 'POST', $paymentJsonReq);
  return $paymentResp;
}

function validateCard($pan, $expDate, $cvv = null, $pin = null, $reqRef)
{
  $authData = $this->interswitch->getAuthData($pan, $expDate, $cvv, $pin);
  $validateReqRef = generateRef();
  $validateReq = array(
   "transactionRef" => $validateReqRef,
   "authData" => $authData
  );
  $validateJsonReq = json_encode($validateReq);
  $validateResp = $this->interswitch->send('api/v2/purchases/validations', 'POST', $validateJsonReq);
  return $validateResp;
}

function verifyOTP($tranId, $otp)
{
  $verifyOTPReq = array(
   "paymentId" => $tranId,
   "otp" => $otp
  );
  $verifyOTPJsonReq = json_encode($verifyOTPReq);
  $verifyOTPResp = $this->interswitch->send('api/v2/purchases/otps/auths', 'POST', $verifyOTPJsonReq);
  return $verifyOTPResp;
}

function getStatus($reqRef, $amt)
{
  $headers = array(
   'amount: ' . $amt,
   'transactionRef: ' . $reqRef
  );
  $statusResp = $this->interswitch->send('api/v2/purchases', 'GET', null, $headers);
  return $statusResp;
}

}

