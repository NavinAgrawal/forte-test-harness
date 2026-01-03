<?php

require_once __DIR__ . '/config/bootstrap.php';
//my sandbox
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$ott               = $_GET['ott'];
//$ott               = 'REDACTED_HASH';
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url . '/organizations/' . $organization_id . '/locations/' . $location_id . '/transactions';

//Credit Card Info
$card = array(
	'card_type' => 'visa',
	'name_on_card' => 'Forte James',
	'one_time_token' => $ott
);

//Address Info
$address = array (
	'first_name' => 'Krista',
	'last_name' => 'Tester',
	'email' => 'integration@forte.net',
	'physical_address' => array (
		'street_line1' => '5058 Tester Street',
		'locality' => 'Testville',
		'region' => 'OH',
		'postal_code' => '45242'
	)
);

$params = array(
	'action' => 'verify',
	'card' => $card,
	'billing_address' => $address,
	//'service_fee_amount' => 1.95,
	'authorization_amount' => 0.00,
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