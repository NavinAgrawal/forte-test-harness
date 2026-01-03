<?php
require_once __DIR__ . '/config/bootstrap.php';
ini_set('max_execution_time', 0);

$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url.'/organizations/'.$organization_id. '/locations/'.$location_id. '/settlements/';



$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_HTTPHEADER, array (
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-Id: ' . $organization_id,
	'Accept: application/json',
	'Content-type: application/json'
));

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
$data = json_decode($response);
$pretty = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

echo '<pre>';
print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
print_r($data);
//var_dump($response);
echo '</pre>';
?>