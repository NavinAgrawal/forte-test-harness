<?php
//this script makes 3 GET calls to see if there is anything in the mid

ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

/////////////////// get schedules count ////////////////////

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/?page_size=1000';
$ch = curl_init($endpoint.'&page_index='.$c);
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
curl_close($ch);
$data = json_decode($response, true);

if($info['http_code']==401) {
	$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}
$schedules_number_results = $data["number_results"];



////////////////// get customer count /////////////////////

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000';
$ch = curl_init($endpoint.'&page_index='.$c);
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
curl_close($ch);
$data = json_decode($response, true);

$customers_number_results = $data["number_results"];



////////////////// get paymethods count //////////////////

$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?page_size=1000';
$ch = curl_init($endpoint.'&page_index='.$c);
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
curl_close($ch);
$data = json_decode($response, true);

$paymethods_number_results = $data["number_results"];

if ($customers_number_results == 0 && $paymethods_number_results == 0 && $schedules_number_results == 0) {
	$message = "There\'s nothing in here chief. You just wasted a nuke.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}
?>