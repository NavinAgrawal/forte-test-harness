<?php
//this script counts the paymethods based on the "number_results" field in the response

ini_set('max_execution_time', 0);

array_map("unlink", glob("paymethods.delete.csv"));

$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/';
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
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}

$message = "There are $number_results paymethods currently in this mid.";
echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";

?>