<?php
//this script deletes all existing customers. First it creates an array of the tokens, then loops thru the array.

ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?orderby=customer_token+asc&page_size=1000';
$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';

$c = 0;
$rowCount = 0;

//Looping GET call to get all the customer tokens
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

	$customers_number_results = $data["number_results"];

	if ($customers_number_results == 0) {
		$customersDeleteCount = 0;
		goto goToPaymethods;
	}

	$substring = "status";
	$tokencount = substr_count($response,$substring);
	
	//store all the tokens in an array
	for($i = 0; $i < $tokencount; $i++){
		$tokens[] = ($data["results"][$i]["customer_token"]);  
	}
	
	//if the word "status" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "status";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		elseif($resultcount >= 1000){
			$c++;
		}
}

$customersDeleteCount = 0;
$scheduleCount = 0;
$numbertokens = count($tokens);
$count = 1;

//delete the customers while looping thru the tokens array
for($i = 0; $i < $numbertokens; $i++) {		
	$endpoint3 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/'.$tokens[$i];
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
	
	if($info['http_code']==200) {
		$customersDeleteCount++;
	}
}
	
$remaining = $customers_number_results - $customersDeleteCount;

$leftovers1 = 'CUSTOMER*';
$leftovers2 = 'AlarmBiller*';
$leftovers3 = 'failure*';
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers1));
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers2));
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers3));

goToPaymethods:
@include('IMPORT-delete.paymethods.php');
?>