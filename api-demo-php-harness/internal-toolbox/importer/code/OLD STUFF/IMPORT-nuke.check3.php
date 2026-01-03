<?php

require_once __DIR__ . '/../../../../config/bootstrap.php';
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);

$endpoint1 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules';
$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers';
$endpoint3 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods';

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));

curl_setopt($ch, CURLOPT_URL, $endpoint1);
$response = curl_exec($ch);
$data1 = json_decode($response, true);

curl_setopt($ch, CURLOPT_URL, $endpoint2);
$response = curl_exec($ch);
$data2 = json_decode($response, true);

curl_setopt($ch, CURLOPT_URL, $endpoint3);
$response = curl_exec($ch);
$data3 = json_decode($response, true);

curl_close($ch);

$schedules_number_results = $data1["number_results"];
$customers_number_results = $data2["number_results"];
$paymethods_number_results = $data3["number_results"];

echo '<pre>';
echo 'Schedules: ' . $schedules_number_results . '<br>';
echo 'Customers: ' . $customers_number_results . '<br>';
echo 'Paymethods: ' . $paymethods_number_results;
echo '</pre>';

?>