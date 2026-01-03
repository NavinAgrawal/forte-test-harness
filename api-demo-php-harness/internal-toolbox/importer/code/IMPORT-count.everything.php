<?php
require_once __DIR__ . '/../../../config/bootstrap.php';

ini_set('max_execution_time', 0);
//array_map("unlink", glob("customers.delete.csv"));



///////////////////////////////////////////
/////////// count the customers  //////////
///////////////////////////////////////////

$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers';

//initialize the curl session and authenticate
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));


$response = curl_exec($ch);
$info = curl_getinfo($ch);	
$data = json_decode($response, true);

$customers_number_results = $data["number_results"];
curl_close($ch);

if($info['http_code']==401) {
	$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
	echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
	exit;
}

if($info['http_code']==400) {
	$message = "HTTP 400 Your REST creds are sucking. Fix them.";
	echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
	exit;
}
//sleep(1);

///////////////////////////////////////////
/////////// count the paymethods //////////
///////////////////////////////////////////

$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods';

//initialize the curl session and authenticate
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));

$response = curl_exec($ch);
$info = curl_getinfo($ch);	
$data = json_decode($response, true);

$paymethods_number_results = $data["number_results"];
//sleep(3);
curl_close($ch);
//sleep(1);

///////////////////////////////////////////
/////////// count the schedules  //////////
///////////////////////////////////////////

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules';
curl_setopt($ch, CURLOPT_URL, $endpoint);

//initialize the curl session and authenticate
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));

$response = curl_exec($ch);
$info = curl_getinfo($ch);	
$data = json_decode($response, true);

$schedules_number_results = $data["number_results"];

//close the curl session
curl_close($ch);


if (strtolower($base_url) == strtolower(forte_config('base_url_production', 'https://api.forte.net/v3'))) {
	$environment = 'Production';
}
if (strtolower($base_url) == strtolower(forte_config('base_url_sandbox', 'https://sandbox.forte.net/api/v3'))) {
	$environment = 'Sandbox';
}
	
$merchant_id = str_replace("loc_","",$location_id);
	
$message = "$environment mid $merchant_id currently has:\\nCustomers: $customers_number_results\\nPaymethods: $paymethods_number_results\\nSchedules: $schedules_number_results";
echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";

?>