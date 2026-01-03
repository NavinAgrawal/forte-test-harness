<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script deletes all of the existing paymethods

/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$filename    = 'CUSTOMER.DATA--MID.'.$merchant_id.'--'.date("Y.m.d").'.csv'; */

ini_set('max_execution_time', 0);
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$endpoint = $_GET['endpoint'];
$endpoint2 = $_GET['endpoint2'];
$auth_token = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
//$base_url = $_GET['base_url'];
//$location_id = $_GET['location_id'];

$delete_set = "paymethods.delete.csv";
$c = 0;
$rowCount = 0;

//Do a GET call and create a csv of paymethod tokens
while($c >= 0){
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
	$rowcount = 0;
	$row = $rowcount;
	
	if ($paymethods_number_results == 0) {
		$message = "There are no paymethods to delete.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
		$paymethod_token = ($data["results"][$i]["paymethod_token"]);
		$sweet = fopen($delete_set, "a+");
		fwrite($sweet, $paymethod_token . PHP_EOL);
		$rowcount++;
		$row++;
	}
	
	// if the word "links" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "links";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		elseif($resultcount >= 1000){
			$c++;
		}
}
$paymethodsDeleteCount = 0;
$paymethodCount = 0;

//loop thru the list created above and delete the paymethods
if (file_exists($delete_set)) {
	$token = NULL;	
	$row = 1;
	if (($handle = fopen("paymethods.delete.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
			$row++;
			for ($c=0; $c < $num; $c++) {
				$token = $data[$c];
				$endpoint3 = $endpoint2.$token;
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
				$data = json_decode($response, true);
				
				if($info['http_code']==200) {
					$paymethodsDeleteCount++;
				}
				
				$paymethod_error = 'Delete failed - There are scheduled transactions for this Payment method.';
				
				if($data["response"]["response_desc"] == "Delete failed - There are scheduled transactions for this Payment method.") {
					$message = "$paymethodsDeleteCount paymethods deleted.\\n\\nYou have paymethods attached to active schedules. You must first delete the schedules, then delete the paymethods.";
					echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
					exit;
				}
			}
		}
		fclose($handle);
	}
	
	//$merchant_id = str_replace("loc_","",$location_id);
	$clientless_paymethods = $paymethods_number_results;
	$paymethods = $paymethods_number_results + $paymethods_number_results;
}

fclose($sweet);
$leftovers1 = 'CUSTOMER*';
$leftovers2 = 'AlarmBiller*';
$leftovers3 = 'failure*';
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers1));
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers2));
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers3));
unlink('paymethods.delete.csv');

if($paymethodsDeleteCount > 0) {
	$message = "$paymethodsDeleteCount paymethods deleted successfully.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
?>