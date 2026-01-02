<?php
//this script makes 3 GET calls to see if there is anything in the mid

ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

/////////////////// get schedules count ////////////////////

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules';
$ch = curl_init($endpoint);
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
//curl_close($ch);
$data = json_decode($response, true);

if($info['http_code']==401) {
	$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}
$schedules_number_results = $data["number_results"];



////////////////// get customer count /////////////////////

$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers';
$ch2 = curl_init($endpoint2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_VERBOSE, 1);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));
$response2 = curl_exec($ch2);
$info2 = curl_getinfo($ch2);	
//curl_close($ch);
$data2 = json_decode($response2, true);

$customers_number_results = $data2["number_results"];



////////////////// get paymethods count //////////////////

$endpoint3    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods';
$ch3 = curl_init($endpoint3);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch3, CURLOPT_VERBOSE, 1);
curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch3, CURLOPT_TIMEOUT, 30);
curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));
$response3 = curl_exec($ch3);
$info3 = curl_getinfo($ch3);	
curl_close($ch3);
$data3 = json_decode($response3, true);

$paymethods_number_results = $data3["number_results"];

if ($customers_number_results == 0 && $paymethods_number_results == 0 && $schedules_number_results == 0) {
	$message = "There\'s nothing in here chief. You just wasted a nuke.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}
?>