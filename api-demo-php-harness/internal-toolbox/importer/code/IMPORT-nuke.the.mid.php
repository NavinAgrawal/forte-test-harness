<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
//this script deletes EVERYTHING in the mid starting with schedules, then customers, then clientless paymethods

date_default_timezone_set('America/Chicago');
ini_set('max_execution_time', 0);
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

///////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////   DELETE SCHEDULES   ////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////

/* $endpoint = $_GET['endpoint'];
$auth_token = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url = $_GET['base_url'];
$location_id = $_GET['location_id'];  */

/*
//This is a sandbox you can play in if you want
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */


/*
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */

//my sandbox
/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key'); */

//integration account - Brittney
/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */

/*
//integration account - Brittney
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */

/*
//PFC - Guam
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */


$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);

//$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/?page_size=1000';
//$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/';


echo date("m/j/Y g:i:s a T") . '<br/><br/>';
$ch = curl_init();
/* curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array (
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept: application/json',
	'Content-type: application/json'
)); */

curl_setopt_array($ch, array(
  //CURLOPT_URL => "https://sandbox.forte.net/api/v3/organizations/org_xxxxx/locations/loc_xxxxx/schedules",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
	"Accept: application/json",
	"X-Forte-Auth-Organization-id: " . $organization_id,
	"Authorization: Basic " . $auth_token
  ),
));


/*
//Do a GET call and create an array of schedule ID's
$c = 0;
$count = 0;
$tokens_schedules = array();

echo 'hello';
exit;

while($c >= 0){
	curl_setopt($ch, CURLOPT_URL, ($endpoint.'&page_index='.$c));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	$response = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$data = json_decode($response, true);
	
	print_r($data);
	exit;
	
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}

	$schedules_number_results = $data["number_results"];

	if ($schedules_number_results == 0) {
		$schedulesDeleteCount = 0;
		goto goToCustomers;
	}
	
	$substring = "schedule_status";
	$tokencount = substr_count($response,$substring);
	
	//store all the tokens in an array
	for($i = 0; $i < $tokencount; $i++){
		$tokens_schedules[] = ($data["results"][$i]["schedule_id"]);  
	}

	// if the word "schedule_status" occurs less than 1000 times in the dataset, break out of the loop
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		elseif($resultcount >= 1000){
			$c++;
		}
}

$schedulesDeleteCount = 0;
$numbertokens = count($tokens_schedules);

//delete the schedules while looping thru the tokens array
for($i = 0; $i < $numbertokens; $i++) {		
	$endpoint3 = $_GET['endpoint2'].$tokens_schedules[$i];
	curl_setopt($ch, CURLOPT_URL, $endpoint3);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$data = json_decode($response, true);
	
	if($info['http_code']==200) {
		$schedulesDeleteCount++;
	}
	if($count == 10){
		sleep(3);
		$count = 0;
	}
}
*/
/////////////////////////// NEW SCHEDULES //////////////////////////////////////

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/';
$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/';
$counter=1;

//Looping GET call to get all the schedule tokens
$c = 0;
$tokens_schedules = array();

while($c >= 0){
	$endpoint3 = $endpoint.'?page_index='.$c;
	curl_setopt($ch, CURLOPT_URL, $endpoint3);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	$response = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$data = json_decode($response, true);

	//echo '<pre>';
	//print_r('HttpStatusCode: ' . $info['http_code'] . '<br>');
	//echo $counter . '<br>';
	//print_r($data);
	//echo '</pre>';


	$schedules_number_results = $data["number_results"];

	if ($schedules_number_results == 0) {
		$schedulesDeleteCount = 0;
		echo 'There are no schedules<br/>';
		goto goToCustomers;
	}

	$substring = "schedule_status";
	$tokencount = substr_count($response,$substring);
	
	//store all the tokens in an array
	for($i = 0; $i < $tokencount; $i++){
		$tokens_schedules[] = ($data["results"][$i]["schedule_id"]);  
	}
	
	//if the word "status" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "schedule_status";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 50){
		$c= -1;
	}
		elseif($resultcount >= 50){
			$c++;
		}
}

echo '<pre>';
print_r ($tokens_schedules);

$schedulesDeleteCount = 0;
$numbertokens = count($tokens_schedules);
$count = 1;
$counter=1;

//delete the schedules while looping thru the tokens array
for($i = 0; $i < $numbertokens; $i++) {		
	$endpoint4 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/'.$tokens_schedules[$i];
	curl_setopt($ch, CURLOPT_URL, $endpoint4);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

	$response = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$data = json_decode($response, true);
	
	echo '<pre>';
	print_r('HttpStatusCode: ' . $info['http_code']);
	echo '<br/>';
	echo date("h:i:s a");
	echo '<br/>';
	echo $counter;
	echo '<br/>';
	print_r($data);
	//echo '</pre>';

	if($info['http_code']==200) {
		$schedulesDeleteCount++;
	}
	//if($count == 10){
		//sleep(3);
		//$count = 0;
	//}
	$counter++;
}

goToCustomers:

///////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////   DELETE CUSTOMERS   ////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=10000';
$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';

//Looping GET call to get all the customer tokens
$c = 0;
$tokens_customers = array();

while($c >= 0){
	$endpoint3 = $endpoint.'&page_index='.$c;
	curl_setopt($ch, CURLOPT_URL, $endpoint3);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	$response = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$data = json_decode($response, true);

	//echo '<pre>';
	//print_r('HttpStatusCode: ' . $info['http_code'] . '<br>');
	//echo $counter . '<br>';
	//print_r($data);
	//echo '</pre>';
	
	$customers_number_results = $data["number_results"];

	if ($customers_number_results == 0) {
		$customersDeleteCount = 0;
		echo 'There are no customers<br/>';
		goto goToPaymethods;
	}

	$substring = "status";
	$tokencount = substr_count($response,$substring);
	
	//store all the tokens in an array
	for($i = 0; $i < $tokencount; $i++){
		$tokens_customers[] = ($data["results"][$i]["customer_token"]);  
	}
	
	//if the word "status" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "status";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 10000){
		$c= -1;
	}
		elseif($resultcount >= 10000){
			$c++;
		}
}

echo '<pre>';
print_r($tokens_customers);

$customersDeleteCount = 0;
$numbertokens = count($tokens_customers);
$count = 1;
$counter=1;

//delete the customers while looping thru the tokens array
for($i = 0; $i < $numbertokens; $i++) {		
	$endpoint4 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/'.$tokens_customers[$i];
	curl_setopt($ch, CURLOPT_URL, $endpoint4);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

	$response = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$data = json_decode($response, true);

	echo '<pre>';
	print_r('HttpStatusCode: ' . $info['http_code']);
	echo '<br/>';
	echo date("h:i:sa");
	echo '<br/>';
	echo $counter;
	echo '<br/>';
	print_r($data);
	//echo '</pre>';
	
	if($info['http_code']==200) {
		$customersDeleteCount++;
	}
	//if($count == 10){
		//sleep(3);
		//$count = 0;
	//}
	$counter++;
}
	
goToPaymethods:

///////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////   DELETE PAYMETHODS   ///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////

$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?page_size=10000';
$endpoint2    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/';

//Looping GET call to get all the paymethod tokens
$c = 0;
$tokens_paymethods = array();

while($c >= 0){
	$endpoint3 = $endpoint.'&page_index='.$c;
	curl_setopt($ch, CURLOPT_URL, $endpoint3);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	$response = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$data = json_decode($response, true);

	//echo '<pre>';
	//print_r('HttpStatusCode: ' . $info['http_code']);
	//echo '<br>';
	//echo $counter;
	//echo '<br>';
	//print_r($data);
	//echo '</pre>'
	
	$paymethods_number_results = $data["number_results"];

	if ($paymethods_number_results == 0) {
		$paymethodsDeleteCount = 0;
		echo 'There are no clientless paymethods';
		goto conclusion;
	}

	$substring = "is_default";
	$tokencount = substr_count($response,$substring);
	
	//store all the tokens in an array
	for($i = 0; $i < $tokencount; $i++){
		$tokens_paymethods[] = ($data["results"][$i]["paymethod_token"]);  
	}
	
	//if the word "is_default" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "is_default";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 10000){
		$c= -1;
	}
		elseif($resultcount >= 10000){
			$c++;
		}
}

echo '<pre>';
print_r ($tokens_paymethods);

$paymethodsDeleteCount = 0;
$numbertokens = count($tokens_paymethods);
$count = 1;
$counter = 1;

//delete the paymethods while looping thru the tokens array
for($i = 0; $i < $numbertokens; $i++) {		
	$endpoint4 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/'.$tokens_paymethods[$i];
	curl_setopt($ch, CURLOPT_URL, $endpoint4);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

	$response = curl_exec($ch);
	$info = curl_getinfo($ch);	
	$data = json_decode($response, true);

	echo '<pre>';
	print_r('HttpStatusCode: ' . $info['http_code']);
	echo '<br/>';
	echo date("h:i:sa");
	echo '<br/>';
	echo $counter;
	echo '<br/>';
	print_r($data);
	//echo '</pre>';
	
	if($info['http_code']==200) {
		$paymethodsDeleteCount++;
	}
	//if($count == 10){
		//sleep(3);
		//$count = 0;
	//}
	$counter++;
}
curl_close($ch);

conclusion:

if (strtolower($base_url) == strtolower(forte_config('base_url_production', 'https://api.forte.net/v3'))) {
	$environment = 'Production';
}

if (strtolower($base_url) == strtolower(forte_config('base_url_sandbox', 'https://sandbox.forte.net/api/v3'))) {
	$environment = 'Sandbox';
}

if ($customers_number_results == 0 && $paymethods_number_results == 0 && $schedules_number_results == 0) {
	$message = "There\'s nothing in here chief. You just wasted a nuke.";
	//echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
	exit;
}

$merchant_id = str_replace("loc_","",$location_id);
$clientless_paymethods = $paymethods_number_results;
$paymethods = $customers_number_results + $paymethods_number_results;

$message = "TOOLBOX 1 says:\\n\\n$environment mid $merchant_id has been nuked without shame or regret.\\n\\nNuked Customers: $customersDeleteCount\\nNuked Clientless Paymethods: $paymethodsDeleteCount\\nNuked Schedules: $schedulesDeleteCount";
//echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
echo '<br/><br/>';
echo date("m/j/Y g:i:s a T");
?>