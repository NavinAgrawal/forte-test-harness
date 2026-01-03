<?php
require_once __DIR__ . '/../../config/bootstrap.php';
// disable timeout and notices
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('America/Chicago');

$base_url          = $_POST['base_url'];
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');

$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url . '/organizations/' . $organization_id . '/locations/' . $location_id . '/transactions';

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');   
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));

//The csv file should have transaction_ID,authorization code. 
$transCount = 1;
$file = fopen("../../internal-toolbox/importer/data.csv","r");
while(! feof($file)){
	$data = fgetcsv($file,0,",","\"");
	$transactionID = "trn_".$data[0];
	$authCode = $data[1];
	
	$params = array( 
		'action' => 'void', 
		'transaction_id' => $transactionID,  
		'authorization_code'=> $authCode,
		'entered_by' => 'Forte Integration'
	);
	
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));     
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);
	$data = json_decode($response);
	
	echo '<pre>';
	echo 'Transaction #' . $transCount . '<br>';
	print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
	print_r($data);
	echo '</pre>'; 
	$transCount++;
} 
curl_close($ch);

?>