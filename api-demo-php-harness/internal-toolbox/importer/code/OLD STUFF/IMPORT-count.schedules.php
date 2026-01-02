<?php
//this script counts the schedules based on the "number_results" field in the response

ini_set('max_execution_time', 0);

array_map("unlink", glob("schedules.delete.csv"));

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
curl_close($ch);
$data = json_decode($response, true);

$number_results = $data["number_results"];

if($info['http_code']==401) {
	$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
	echo "<script type='text/javascript'>if(confirm('$message')) window.history.back();</script>";
	exit;
}

if ($number_results == 0) {
	$message = "There are 0 schedules currently in this mid.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
if ($number_results == 1) {
	$message = "There is 1 active schedule in this mid.\\nYou must delete the schedule before you can delete the customer.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
if ($number_results >= 2) {
	$message = "There are $number_results active schedules in this mid.\\nYou must delete the schedules before you can delete the customers.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
//$message = "There are $number_results schedules currently in this mid.";
//echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";

?>