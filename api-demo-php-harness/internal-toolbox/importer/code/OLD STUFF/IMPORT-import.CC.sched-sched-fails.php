<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script imports the CC data

date_default_timezone_set('America/Chicago');
ini_set('max_execution_time', 0);

/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';  */

$error_no = 1;
$sched_error_no = 1;
$sched_success = 0;
$sched_fail = 0;

$fail_schedLog = fopen("../../../internal-toolbox/importer/failure.log2.sched-CC.txt", "a+");
fwrite($fail_schedLog, "
////////////////////////////////////////////////////////////////
///////////                                         ////////////
///////////   C C   S C H E D U L E   E R R O R S   ////////////
///////////                                         ////////////
////////////////////////////////////////////////////////////////

");
$sched_log_stamp = date('n/j/Y g:i A T');
fwrite($fail_schedLog, $sched_log_stamp . "\r\n");
fwrite($fail_schedLog, "========================\r\n");

if ($handle = fopen("../../../internal-toolbox/importer/failure.CC.schedule.data.csv", "a+")) {
	while ($fields = fgetcsv($handle)) {
		
		//define the dataset variables
		$cust_token        = $fields[0];
		$pay_token         = $fields[1];
		$sched_quantity    = $fields[2];
		$frequency         = $fields[3];
		$sched_amount      = $fields[4];
		$sched_start_date  = $fields[5];

		//strip off these characters during the import
		$badcharacters = array(',','"',"'",'~','!','#','$','%','^','*','(',')');
		
		$sched_params = array (
			'action' => 'sale',
			'customer_token' => $cust_token,
			'paymethod_token' => $pay_token,
			'schedule_quantity' => $sched_quantity,
			'schedule_frequency' => $frequency,
			'schedule_amount' => $sched_amount,
			'schedule_start_date' => $sched_start_date
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
		
		if($info['http_code']==401) {
			$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
			echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
			exit;
		}
		if($info['http_code']==201) {
			$sched_success++;
		}
	
		//create the failure data file
		$fail_schedData = fopen("../../../internal-toolbox/importer/failure.data2.sched-CC.csv", "a+");
		
		$sched_data = array($cust_token,$pay_token,$sched_quantity,$frequency,$sched_amount,$sched_start_date);
		
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
}

if($sched_fail == 0) {
	fwrite($fail_schedLog, 'Congratulations! No failures.' . "\r\n\r\n");
	$message = "Congratulations! No failures.\\nAll $sched_success schedules imported successfully.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
} 
if($sched_fail >= 1) {
	$message = "Successful Schedules: $sched_success\\nFailed Schedules: $sched_fail";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
fclose($handle);
fclose($fail_schedData);
fclose($fail_schedLog);

rename("../../../internal-toolbox/importer/failure.log2.sched-CC.txt","../../../internal-toolbox/importer/failure.CC.schedule.log.txt");
rename("../../../internal-toolbox/importer/failure.data2.sched-CC.csv","../../../internal-toolbox/importer/failure.CC.schedule.data.csv");
?>