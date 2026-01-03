<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script deletes all existing customers. First it creates a file of the tokens, then loops thru the tokens.

/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$merchant_id       = str_replace("loc_","",$location_id);
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$filename          = 'CUSTOMER.DATA--MID.'.$merchant_id.'--'.date("Y.m.d").'.csv';
$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000';
$endpoint2         = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';  */

$endpoint        = $_GET['endpoint'];
$endpoint2       = $_GET['endpoint2'];
$auth_token      = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url        = $_GET['base_url'];
$location_id     = $_GET['location_id'];

ini_set('max_execution_time', 0);
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$delete_set = "../../../internal-toolbox/importer/undo.import.ACH.csv";
$c = 0;
$rowCount = 0;

$customersDeleteCount = 0;
$customersNoMatchCount = 0;
$sweet = fopen($delete_set, "a+");

//loop thru the list created above and delete the customers
$token = NULL;	
$row = 1;
$handle = fopen("../../../internal-toolbox/importer/undo.import.ACH.csv", "r");
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	$num = count($data);
	$row++;
	for ($c=0; $c < $num; $c++) {
		$token = $data[$c];
		$endpoint3 = $endpoint2 . 'cst_' . $token;
		$ch = curl_init($endpoint3);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Basic ' . $auth_token,
			'X-Forte-Auth-Organization-id: ' . $organization_id,
			'Accept:application/json',
			'Content-type: application/json'
		));
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		$data = json_decode($response,true);
		
		if($info['http_code']==200) {
			$customersDeleteCount++;
		}
		if($info['http_code']==400) {
			$customersNoMatchCount++;
		}
	}
}
fclose($handle);
fclose($sweet);

if($customersDeleteCount > 0) {
	$message = "TOOLBOX 1 says:\\n\\nUndo import has been completed.\\n\\n$customersDeleteCount ACH customers have been deleted.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
if($customersDeleteCount == 0) {
		$message = "TOOLBOX 1 says:\\n\\nNone of the existing customers matched up with the tokens file.\\n\\n$customersDeleteCount ACH customers have been deleted.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	}

?>