<?php

require_once __DIR__ . '/../../config/bootstrap.php';
// disable timeout and notices
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('America/Chicago');

/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */

$base_url          = $_POST['base_url'];
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');

$transactionID     = $_POST['trans_id'];
$authCode          = $_POST['auth_code'];
$amount            = $_POST['amount'];

$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/transactions';

$params = array(
	'action' => 'reverse',  
	'original_transaction_id' => 'trn_' . $transactionID,  
	'authorization_code'=> $authCode,
	'authorization_amount'=> $amount
);

//$params = $_POST;

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');                //POST, GET, PUT or DELETE (Create, Find, Update or Delete)
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));     //Disable this line for GET's and DELETE's
curl_setopt($ch, CURLOPT_HTTPHEADER, array (
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept: application/json',
	'Content-type: application/json'
));

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
$data = json_decode($response);


echo '<pre>';
print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
print_r($data);
echo '</pre>';
?>