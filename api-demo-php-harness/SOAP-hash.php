<?php
require_once __DIR__ . '/config/bootstrap.php';

//this script calculates the UTC time and hash required for SOAP calls

// getUTC endpoints
// https://sandbox.forte.net/checkout/getUTC?callback=?  //sandbox
// https://checkout.forte.net/getUTC?callback=?          //production

	function utc() {
		$curlUTC = curl_init();
		curl_setopt($curlUTC, CURLOPT_URL, 'https://checkout.forte.net/getUTC?callback=?');
		curl_setopt($curlUTC, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($curlUTC, CURLOPT_RETURNTRANSFER, true);
		
		$curlData = (curl_exec($curlUTC));
		$stripOpenParen = stripos($curlData,"(");
		$stripClosedParen = stripos($curlData,")");		
		$utc = substr($curlData,$stripOpenParen+1,$stripClosedParen-2);
		return $utc;
		curl_close($curlUTC);
	}

$merchantID       = 173185;
$APILoginID       = forte_config('api_login_id');
$SecureTransKey   = 'X2sMcZJaYO';
//$millitime        = microtime(true) * 1000;
//$utc              = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
$utc = utc();
$data             = "$APILoginID|$utc";
$hash             = hash_hmac('md5', $data, $SecureTransKey);

/* $merchantID       = 252862;
$APILoginID       = forte_config('api_login_id');
$SecureTransKey   = 'JFihJjmXn24K6URBNcc4G';
//$millitime        = microtime(true) * 1000;
//$utc              = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
$utc = utc();
$data             = "$APILoginID|$utc";
$hash             = hash_hmac('md5', $data, $SecureTransKey);  */

/* $merchantID       = 173185;
$APILoginID       = forte_config('api_login_id');
$SecureTransKey   = 'xbTFuKFI4eVYjb';
//$millitime        = microtime(true) * 1000;
//$utc              = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
$utc              = utc();
$data             = "$APILoginID|$utc";
$hash             = hash_hmac('md5', $data, $SecureTransKey); */

echo ('<div style="margin-left:40%"><br>');
echo ('MerchantID: &nbsp;' . $merchantID . '<br><br>');
echo ('APILoginID: &nbsp;' . $APILoginID . '<br><br>');
echo ('TSHash: &nbsp;' . $hash . '<br><br>');
echo ('UTCTime: &nbsp;' . $utc);

?>