<?php

require_once __DIR__ . '/config/bootstrap.php';
//173185 production
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url . '/organizations/' . $organization_id . '/locations/' . $location_id . '/transactions';

//Credit Card Info
$card = array(
	'card_type' => 'visa',
	'name_on_card' => 'James Ivey',
	'one_time_token' => 'ott__*****************'
);

$params = array(
	'action' => 'sale',
	'card' => $card,
	//'service_fee_amount' => 1.95,
	//'billing_address' => $address,
	'authorization_amount' => 0.01,
	'save_token' => 'customer'		
);

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
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