<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script deletes all existing paymethods. First it creates an array of the tokens, then loops thru the array.

ini_set('max_execution_time', 0);
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?page_size=2';
$endpoint2    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/';

$c = 0;
$rowCount = 0;

//Looping GET call to get all the paymethod tokens
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

	$substring = "links";
	$tokencount = substr_count($response,$substring);
	
	//store all the tokens in an array
	for($i = 0; $i < $tokencount; $i++){
		$tokens[] = ($data["results"][$i]["customer_token"]);  
	}
print_r ($tokens);
	
	//if the word "links" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "links";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 2){
		$c= -1;
	}
		elseif($resultcount >= 2){
			$c++;
		}
}

$paymethodsDeleteCount = 0;
$paymethodCount = 0;
$numbertokens = count($tokens);
$count = 1;

//delete the paymethods while looping thru the tokens array
for($i = 0; $i < $numbertokens; $i++) {		
	$endpoint3 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/'.$tokens[$i];
	$ch = curl_init($endpoint3);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
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

echo '<pre>';
print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
print_r($data);
echo '</pre>';
	
	if($info['http_code']==200) {
		$paymethodsDeleteCount++;
	}
	
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
	
$leftovers1 = 'CUSTOMER*';
$leftovers2 = 'AlarmBiller*';
$leftovers3 = 'failure*';
array_map("unlink", glob('../../../toolbox1/importer/' . $leftovers1));
array_map("unlink", glob('../../../toolbox1/importer/' . $leftovers2));
array_map("unlink", glob('../../../toolbox1/importer/' . $leftovers3));
?>