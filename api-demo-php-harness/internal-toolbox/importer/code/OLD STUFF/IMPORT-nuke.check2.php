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

// build the individual requests, but do not execute them
$ch1 = curl_init($endpoint1);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));

$ch2 = curl_init($endpoint2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));

$ch3 = curl_init($endpoint3);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));
  
// build the multi-curl handle, adding all three $ch
$mh = curl_multi_init();
curl_multi_add_handle($mh, $ch1);
curl_multi_add_handle($mh, $ch2);
curl_multi_add_handle($mh, $ch3);
  
// execute all queries, and continue when all are complete
$running = null;
do {
	curl_multi_exec($mh, $running);
} while ($running);

// close the handles
curl_multi_remove_handle($mh, $ch1);
curl_multi_remove_handle($mh, $ch2);
curl_multi_remove_handle($mh, $ch3);
curl_multi_close($mh);
  
// all of our requests are done, we can now access the results
$response1 = curl_multi_getcontent($ch1);
$response2 = curl_multi_getcontent($ch2);
$response3 = curl_multi_getcontent($ch3);

$data1 = json_decode($response1, true);
$data2 = json_decode($response2, true);
$data3 = json_decode($response3, true);

$schedules_number_results = $data1["number_results"];
$customers_number_results = $data2["number_results"];
$paymethods_number_results = $data3["number_results"];

echo '<pre>';
echo 'Schedules: ' . $schedules_number_results . '<br>';
echo 'Customers: ' . $customers_number_results . '<br>';
echo 'Paymethods: ' . $paymethods_number_results;
echo '</pre>';
exit;

?>