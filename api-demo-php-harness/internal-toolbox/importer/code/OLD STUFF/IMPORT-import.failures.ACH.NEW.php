<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script imports the ACH failures

date_default_timezone_set('America/Chicago');
ini_set('max_execution_time', 0);

if (!file_exists("../../../internal-toolbox/importer/failure.ACH.data.csv")) {
	$message = "There are no failures to import.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}

////////////////////////////////////////////////////////////////////////

/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */

//This is a sandbox you can play in if you want
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
//$filename    = 'CUSTOMER.DATA--MID.'.$merchant_id.'--'.date("Y.m.d").'.csv';
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers';

////////////////////////////////////////////////////////////////////////

/* $endpoint = $_GET['endpoint'];
$auth_token = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url = $_GET['base_url'];
$location_id = $_GET['location_id']; */

$error_no = 1;
$success = 0;
$fail = 0;
$count = 1;
$throttle = 1;

// open the tokens file and write the header row
$tokens = fopen("../../../internal-toolbox/importer/tokens.ACH.csv", "a+");

//strip off these characters during the import
$badcharacters = array(',','~','!','$','%','^','*','(',')');

$handle = fopen("../../../internal-toolbox/importer/failure.ACH.data.csv", "r");
while ($fields = fgetcsv($handle, 10000, ",")) {
	
	//assign the dataset variables
	$firstname      = $fields[0];
	$lastname       = $fields[1];
	$companyname    = $fields[2];
	$address1       = $fields[3];
	$address2       = $fields[4];
	$city           = $fields[5];
	$state          = $fields[6];
	$zipcode        = $fields[7];
	$phone          = $fields[8];
	$email          = $fields[9];
	$customer_id    = $fields[10];
	$account_holder = $fields[11];
	$routing_number = $fields[12];
	$account_number = $fields[13];
	$checking_type  = $fields[14];

	//strip off these characters during the import
	$badcharacters = array(',','~','!','$','%','^','*','(',')');
	
	$params = array (
		'first_name' => str_replace($badcharacters,"",$firstname),
		'last_name' => str_replace($badcharacters,"",$lastname),
		'company_name' => str_replace($badcharacters,"",$companyname),
		'customer_id' => str_replace($badcharacters,"",$customer_id),
		'addresses' => array ( 
			array (
				'first_name' => str_replace($badcharacters,"",$firstname),
				'last_name' => str_replace($badcharacters,"",$lastname),
				'email' => str_replace($badcharacters,"",$email),
				'phone' => str_replace($badcharacters,"",$phone),
				'address_type' => 'default_billing',
				'physical_address' => array (
					'street_line1' => str_replace($badcharacters,"",$address1),
					'street_line2' => str_replace($badcharacters,"",$address2),
					'locality' => str_replace($badcharacters,"",$city),
					'region' => str_replace($badcharacters,"",$state),
					'postal_code' => $zipcode
				)
			)
		),
		'paymethod' => array (
			//'notes' => $counter,
			'echeck' => array (
				'account_holder' => str_replace($badcharacters,"",$account_holder),
				'routing_number' => $routing_number,
				'account_number' => $account_number,
				'account_type' => $checking_type,
			),
		)
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
	$data = json_decode($response,true);

	echo '<pre>';
	print_r('HttpStatusCode: ' . $info['http_code'] . '<br>');
	echo $count . ' record count<br><br>';
	print_r($data);
	echo $throttle . ' throttle';
	echo '</pre>';
	
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	//if the failure2 log does not exist, create it and write the header
	if (!file_exists('../../../internal-toolbox/importer/failure.log2.ACH.txt')) {
		$fail_log = fopen("../../../internal-toolbox/importer/failure.log2.ACH.txt", "a+");
		fwrite($fail_log, "
////////////////////////////////////////////////////////////////
///////////////////                         ////////////////////
///////////////////   A C H   E R R O R S   ////////////////////
///////////////////                         ////////////////////
////////////////////////////////////////////////////////////////

");
		$log_stamp = date('n/j/Y g:i A T');
		fwrite($fail_log, $log_stamp . "\r\n");
		fwrite($fail_log, "========================\r\n");		
	}
	
	//create the failure data file
	$fail_data = fopen("../../../internal-toolbox/importer/failure.data2.ACH.csv", "a+");
	
	//if create fails, write it to the failure files
	if($info['http_code']==400) {
		$result = json_decode($response);
		$error = ($result->response->response_desc);
		fputcsv($fail_data, $fields);
		fwrite($fail_log, 'Line ' . $error_no . "\r\n");
		fwrite($fail_log, $error ."\r\n\r\n");
		$error_no++;
		$fail++;
	} 
	
	//if create is successful, write the record (with tokens) to the tokens file
	if($info['http_code']==201) {
		$success++;
		$count++;
		$fields[11] = substr($fields[11],-4);  //convert card number to last 4 only	
		$customer_token  = str_replace("cst_","",($data["customer_token"]));
		$paymethod_token = str_replace("mth_","",($data["default_paymethod_token"]));
		fwrite($tokens, $customer_token.','.$paymethod_token.',');
		fputcsv($tokens, str_replace($badcharacters,"",$fields));
	}
	
	//throttle the calls to about 40 per second
	if($throttle < 40){
		$throttle++;
	}
	elseif($throttle = 40){
		$micro_seconds = microtime(false) * 1000000;
		echo date('H:i:s:'. floor($micro_seconds)) . '<br>';
		usleep(250000);
		$micro_seconds = microtime(false) * 1000000;
		echo date('H:i:s:'. floor($micro_seconds));
		$throttle = 1;
	}
}

fclose($handle);
	
/* if($fail == 0) {
	fwrite($fail_log, 'Congratulations! No failures.' . "\r\n\r\n");
	$message = "Congratulations! No failures.\\n$success records imported successfully.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}	else {
		$message = "$success customers imported successfully.\\n$fail failures.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	}  */

fclose($fail_data);
fclose($fail_log);
unlink("../../../internal-toolbox/importer/failure.ACH.data.csv");
unlink("../../../internal-toolbox/importer/failure.ACH.log.txt");
rename("../../../internal-toolbox/importer/failure.data2.ACH.csv","../../../internal-toolbox/importer/failure.ACH.data.csv");
rename("../../../internal-toolbox/importer/failure.log2.ACH.txt","../../../internal-toolbox/importer/failure.ACH.log.txt");
?>