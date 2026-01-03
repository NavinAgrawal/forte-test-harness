<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script imports the ACH failures

date_default_timezone_set('America/Chicago');
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (!file_exists("../../../internal-toolbox/importer/failure.ACH.data.csv")) {
	$message = "There are no failures to import.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}

$endpoint = $_GET['endpoint'];
$auth_token = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url = $_GET['base_url'];
$location_id = $_GET['location_id'];

/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);  */

$error_no = 1;
$success = 0;
$fail = 0;

$handle = fopen("../../../internal-toolbox/importer/failure.ACH.data.csv", "r");
while ($fields = fgetcsv($handle, 1000, ",")) {
	
	//define the dataset variables
	$firstname      = $fields[0];
	$lastname       = $fields[1];
	$family_id      = $fields[2];
	$payment_id     = $fields[3];
	$account_holder = $fields[4];
	$routing_number = $fields[5];
	$account_number = $fields[6];
	$checking_type  = $fields[7];
	
	//strip off these characters during the import
	$badcharacters = array(',','"',"'",'~','!','#','$','%','^','*','(',')');
	
	$params = array (
		'first_name' => str_replace($badcharacters,"",$firstname),
		'last_name' => str_replace($badcharacters,"",$lastname),
		'customer_id' => str_replace($badcharacters,"",$family_id),
		'addresses' => array ( 
			array (
				'first_name' => str_replace($badcharacters,"",$firstname),
				'last_name' => str_replace($badcharacters,"",$lastname),
				'address_type' => 'default_billing',
			)
		),
		'paymethod' => array (
			'notes' => $payment_id,
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
//////////////////                         /////////////////////
//////////////////   A C H   E R R O R S   /////////////////////
//////////////////                         /////////////////////
////////////////////////////////////////////////////////////////

");
		$log_stamp = date('n/j/Y g:i A T');
		fwrite($fail_log, $log_stamp . "\r\n");
		fwrite($fail_log, "========================\r\n");		
	}
	
	//create the failure data file
	$fail_data = fopen("../../../internal-toolbox/importer/failure.data2.ACH.csv", "a+");
	
	//if import fails, write it to the failure files
	if($info['http_code']==400) {
		$result = json_decode($response);
		$error = ($result->response->response_desc);
		fputcsv($fail_data, $fields);
		fwrite($fail_log, 'Line ' . $error_no . "\r\n");
		fwrite($fail_log, $error ."\r\n\r\n");
		$error_no++;
		$fail++;
	} 
	
	if($info['http_code']==201) {
		$success++;			
	}
}

fclose($handle);
	
if($fail == 0) {
	fwrite($fail_log, 'Congratulations! No failures.' . "\r\n\r\n");
	$message = "Congratulations! No failures.\\n$success records imported successfully.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}	else {
		$message = "$success customers imported successfully.\\n$fail failures.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	}

fclose($fail_data);
fclose($fail_log);
unlink("../../../internal-toolbox/importer/failure.ACH.data.csv");
unlink("../../../internal-toolbox/importer/failure.ACH.log.txt");
rename("../../../internal-toolbox/importer/failure.data2.ACH.csv","../../../internal-toolbox/importer/failure.ACH.data.csv");
rename("../../../internal-toolbox/importer/failure.log2.ACH.txt","../../../internal-toolbox/importer/failure.ACH.log.txt");
?>