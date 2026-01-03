<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script deletes all of the existing paymethods

ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?page_size=1000';
$endpoint2    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/';

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
		$paymethodsDeleteCount = 0;
		goto conclusion;
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
		while (($data = fgetcsv($handle)) !== FALSE) {
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
				$data = json_decode($response);
				
				$paymethod_error = 'Delete failed - There are scheduled transactions for this Payment method.';

				if($info['http_code']==200) {
					$paymethodsDeleteCount++;
				}
			}
		}
		fclose($handle);
		
		conclusion:
		if (strtolower($base_url) == strtolower(forte_config('base_url_production', 'https://api.forte.net/v3'))) {
			$environment = 'Production';
		}
		if (strtolower($base_url) == strtolower(forte_config('base_url_sandbox', 'https://sandbox.forte.net/api/v3'))) {
			$environment = 'Sandbox';
		}
		
		if ($customers_number_results == 0 && $paymethods_number_results == 0 && $schedules_number_results == 0) {
			$message = "There\'s nothing in here chief. You just wasted a nuke.";
			echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
			exit;
		}
	
		$merchant_id = str_replace("loc_","",$location_id);
		$clientless_paymethods = $paymethods_number_results;
		$paymethods = $customers_number_results + $paymethods_number_results;
		
		$message = "TOOLBOX 1 says:\\n\\n$environment mid $merchant_id has been nuked without shame or regret.\\n\\nNuked Customers: $customersDeleteCount\\nNuked Paymethods: $paymethods\\nNuked Schedules: $schedulesDeleteCount";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
	}
}

fclose($sweet);
$leftovers1 = 'CUSTOMER*';
$leftovers2 = 'AlarmBiller*';
$leftovers3 = 'failure*';
array_map("unlink", glob('../../../toolbox1/importer/' . $leftovers1));
array_map("unlink", glob('../../../toolbox1/importer/' . $leftovers2));
array_map("unlink", glob('../../../toolbox1/importer/' . $leftovers3));
unlink('paymethods.delete.csv');
?>