<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
//this script imports ACH customer schedules

date_default_timezone_set('America/Chicago');
ini_set('max_execution_time', 0);
array_map("unlink", glob("../failure.ACH.customer.data.csv"));
array_map("unlink", glob("../failure.ACH.customer.log.txt"));
array_map("unlink", glob("../failure.ACH.schedule.data.csv"));
array_map("unlink", glob("../failure.ACH.schedule.log.txt"));

echo date("h:i:sa");
echo '<br/><br/>';

//Aikido - PRODUCTION
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
/*
//PFC - Guam - PRODUCTION
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */

/*
//PFC - Guam - SANDBOX
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */


$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';

/*
//capture these variables from the importer engine
$endpoint        = $_GET['endpoint'];
$auth_token      = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url        = $_GET['base_url'];
$location_id     = $_GET['location_id'];  */

$cust_error_no = 1;
$sched_error_no = 1;
$cust_success = 0;
$sched_success = 0;
$cust_fail = 0;
$sched_fail = 0;
$count = 1;

$handle = fopen("../data.ACH.csv", "r");
while ($fields = fgetcsv($handle)) {
	
	//define the dataset variables
	$counter          = $fields[0];
	$firstname        = $fields[1];
	$lastname         = $fields[2];
	$companyname      = $fields[3];
	$address1         = $fields[4];
	$address2         = $fields[5];
	$city             = $fields[6];
	$state            = $fields[7];
	$zipcode          = $fields[8];
	$phone            = $fields[9];
	$email            = $fields[10];
	$customer_id      = $fields[11];
	$accountholder    = $fields[12];
	$routing_number   = $fields[13];
	$account_number   = $fields[14];
	$account_type     = $fields[15];
	$sched_quantity   = $fields[16];
	$frequency        = $fields[17];
	$sched_amount     = $fields[18];
	$sched_start_date = $fields[19];
	$sec_code         = $fields[20];

	//strip off these characters during the import
	$badcharacters = array(',','"',"'",'~','!','#','$','%','^','*','(',')');
	
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
					'postal_code' => str_replace($badcharacters,"",$zipcode)
				)
			)
		),
		'paymethod' => array (
			'notes' => $counter,
			'echeck' => array (
				'account_type' => $account_type,
				'account_holder' => str_replace($badcharacters,"",$accountholder),
				'routing_number' => $routing_number,
				'account_number' => $account_number

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
	$data = json_decode($response, true);
	
	echo '<pre>';
	echo $count;
	echo '<br/>';
	print_r('HttpStatusCode: ' . $info['http_code']);
	echo '<br/>';
	print_r($data);
	
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	//if the failure log does not exist, create it and write the header
	if (!file_exists('../failure.ACH.customer.log.txt')) {
		$fail_custLog = fopen("../failure.ACH.customer.log.txt", "a+");
		fwrite($fail_custLog, "
////////////////////////////////////////////////////////////////
//////////                                           ///////////
//////////   A C H   C U S T O M E R   E R R O R S   ///////////
//////////                                           ///////////
////////////////////////////////////////////////////////////////

");
		$log_stamp = date('n/j/Y g:i A T');
		fwrite($fail_custLog, $log_stamp . "\r\n");
		fwrite($fail_custLog, "========================\r\n");		
	}
	
	//create the customer failure data file
	$fail_custData = fopen("../failure.ACH.customer.data.csv", "a+");
	
	//if create fails, write it to the failure files
	if($info['http_code']==400) {
		$result = json_decode($response);
		$error = ($result->response->response_desc);
		fputcsv($fail_custData, $fields);
		fwrite($fail_custLog, 'Line ' . $cust_error_no . "\r\n");
		fwrite($fail_custLog, $error ."\r\n\r\n");
		$cust_error_no++;
		$cust_fail++;
		goto nextRecord;
	}
	
	if($info['http_code']==201) {
		$cust_success++;
	
		$cust_token = $data["customer_token"];
		$pay_token = $data["default_paymethod_token"];
		
		$sched_params = array (
			'action' => 'sale',
			'customer_token' => $cust_token,
			'paymethod_token' => $pay_token,
			'schedule_quantity' => $sched_quantity,
			'schedule_frequency' => $frequency,
			'schedule_amount' => $sched_amount,
			'schedule_start_date' => $sched_start_date,
			'sec_code' => $sec_code
		);

		$endpoint2    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/';			
		$ch = curl_init($endpoint2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sched_params));
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
		echo date("h:i:sa");
		echo '<br/>';
		echo $count;
		echo '<br/>';
		print_r('HttpStatusCode: ' . $info['http_code']);
		echo '<br/>';
		print_r($data);
		$counter++;

		//if the failure log does not exist, create it and write the header
		if (!file_exists('../failure.ACH.schedule.log.txt')) {
			$fail_schedLog = fopen("../failure.ACH.schedule.log.txt", "a+");
			fwrite($fail_schedLog, "
////////////////////////////////////////////////////////////////
//////////                                           ///////////
//////////   A C H   S C H E D U L E   E R R O R S   ///////////
//////////                                           ///////////
////////////////////////////////////////////////////////////////

");
			$sched_log_stamp = date('n/j/Y g:i A T');
			fwrite($fail_schedLog, $sched_log_stamp . "\r\n");
			fwrite($fail_schedLog, "========================\r\n");		
		}
		
		if($info['http_code']==201) {
			$sched_success++;
		}				
		
		//create the schedule failure data file
		$fail_schedData = fopen("../failure.ACH.schedule.data.csv", "a+");
		
		$fullname = $firstname . ' ' . $lastname;
		
		$sched_data = array($fullname,$companyname,$customer_id,$cust_token,$pay_token,$sched_quantity,$frequency,$sched_amount,$sched_start_date,$sec_code);
		
		//if create fails, write it to the failure files
		if($info['http_code']==400) {
			$result = json_decode($response);
			$error = ($result->response->response_desc);
			fputcsv($fail_schedData, $sched_data);
			fwrite($fail_schedLog, 'Line ' . $sched_error_no . "\r\n");
			fwrite($fail_schedLog, $error ."\r\n\r\n");
			$sched_error_no++;
			$sched_fail++;
		}		
	}
	nextRecord:
}

$total_sched_fail = ($cust_fail + $sched_fail);

if($cust_fail == 0 && $sched_fail == 0) {
	fwrite($fail_custLog, 'Congratulations! No failures.' . "\r\n\r\n");
	fwrite($fail_schedLog, 'Congratulations! No failures.' . "\r\n\r\n");
	$message = "Congratulations! No failures.\\n\\nAll $cust_success customer records imported successfully.\\nAll $sched_success schedules imported successfully.";
	echo "<script type='text/javascript'>confirm('$message');</script>";
	//echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
if($cust_fail == 0 && $sched_fail >= 1) {
	fwrite($fail_custLog, 'Congratulations! No failures.' . "\r\n\r\n");
	$message = "Successful Customers: $cust_success\\nSuccessful Schedules: $sched_success\\nFailed Customers: $cust_fail\\nFailed Schedules: $sched_fail";
	echo "<script type='text/javascript'>confirm('$message');</script>";
	//echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
if($cust_fail >= 1 && $sched_fail == 0) {
	fwrite($fail_schedLog, 'Congratulations! No failures.' . "\r\n\r\n");
	$message = "Successful Customers: $cust_success\\nSuccessful Schedules: $sched_success\\nFailed Customers: $cust_fail\\nFailed Schedules: $sched_fail";
	echo "<script type='text/javascript'>confirm('$message');</script>";
	//echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
if($cust_fail >= 1 && $sched_fail >= 1) {
	$message = "Successful Customers: $cust_success\\nSuccessful Schedules: $sched_success\\nFailed Customers: $cust_fail\\nFailed Schedules: $sched_fail";
	echo "<script type='text/javascript'>confirm('$message');</script>";
	//echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}

fclose($handle);
fclose($fail_custData);
fclose($fail_custLog);
fclose($fail_schedData);
fclose($fail_schedLog);

echo '<br/><br/>';
echo date("h:i:sa");
?>