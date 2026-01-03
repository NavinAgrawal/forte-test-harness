<?php
//this script exports all of the schedules

// disable timeout and notices
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('America/Chicago');

unlink("customer.list.csv");
unlink("paymethod.list.csv");
unlink("schedule.list.csv");

$auth_token      = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url        = $_GET['base_url'];
$location_id     = $_GET['location_id'];

$merchant_id = str_replace("loc_","",$location_id);
$filename    = 'SCHEDULES-'.date("Y.m.d").'.csv';

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/';
$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/';

$schedule_set = "schedule.list.csv";
$customer_set = "customer.list.csv";
$paymethod_set = "paymethod.list.csv";

$count = 0;
$c = 0;
$p = 0;
$s = 0;

//Do a GET call and create a csv of schedule_id, customer_token and paymethod_token
while($c >= 0){
	$ch = curl_init($endpoint.'?page_index='.$c);
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
	$number_results = $data["number_results"];
	
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	
	if($number_results == 0) {
		$message = "There are no schedules to export.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
		exit;
	}
	
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
		$schedule_id = ($data["results"][$i]["schedule_id"]);
		$cust_token = ($data["results"][$i]["customer_token"]);
		$pay_token = ($data["results"][$i]["paymethod_token"]);
		$schedules = fopen($schedule_set, "a+");
		$customers = fopen($customer_set, "a+");
		$paymethods = fopen($paymethod_set, "a+");
		fwrite($schedules, $schedule_id . PHP_EOL);
		fwrite($customers, $cust_token . PHP_EOL);
		fwrite($paymethods, $pay_token . PHP_EOL);
	}
	
	// if the word "action" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "action";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 50){
		$c= -1;
	}
		elseif($resultcount >= 50){
			$c++;
		}
};

// Now a GET on customer token for customer info, and a GET on the schedule_id for schedule details
$c = 0;
$p = 0;
$s = 0;

// define the csv header row
$headers = '"Consumer ID","Name","Company Name","Phone","Email","Schedule Status","Start Date","Frequency","Next Payment","Final Payment","Total Pmts","Remaining Pmts","Successful Pmts","Failed Pmts","Principal Amt","Service Fee Amt","Total Payment Amt","Remaining Balance"'.PHP_EOL;

// write the csv header row
$newfile = fopen('../'.$filename,"w+");
fwrite($newfile, $headers);

//do the GET on the customer token
if (file_exists($paymethod_set)) {
	$cust_token = NULL && $pay_token = NULL && $sched_token = NULL;	
	if (($cust_handle = fopen("customer.list.csv", "r+")) && ($pay_handle = fopen("paymethod.list.csv", "r+")) && ($sched_handle = fopen("schedule.list.csv", "r+")) !== FALSE) {
		while (($cust_data = fgetcsv($cust_handle, ",")) && ($pay_data = fgetcsv($pay_handle, ",")) && ($sched_data = fgetcsv($sched_handle, ",")) !== FALSE) {
			$c_num = count($cust_data);
			$p_num = count($pay_data);
			$s_num = count($sched_data);
			$cust_token = $cust_data[$c];
			$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
			
			$ch = curl_init($endpoint . $cust_token);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Basic ' . $auth_token,
				'X-Forte-Auth-Organization-id: ' . $organization_id,
				'Accept:application/json',
				'Content-type: application/json'
			));
			$cust_response = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);
			$cust_result = json_decode($cust_response, true);
				
		    //do the GET on the schedule ID
			$sched_token = $sched_data[$s];
			$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/';
			
			$ch = curl_init($endpoint . $sched_token);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Basic ' . $auth_token,
				'X-Forte-Auth-Organization-id: ' . $organization_id,
				'Accept:application/json',
				'Content-type: application/json'
			));
			$sched_response = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);
			$sched_result = json_decode($sched_response, true);
				
			//strip away these characters
			$badcharacters = array(',','"',"'",'~','!','#','$','%','^','*','(',')');
			
			// define the variables for writing the csv file
			$merchant_id       = str_replace("loc_","",($cust_result["location_id"]));
			$customer_token    = str_replace("cst_","",($cust_result["customer_token"]));
			$paymethod_token   = str_replace("mth_","",($pay_result["paymethod_token"]));
			$customer_id       = $cust_result["customer_id"];
			$status            = $cust_result["status"];
			$firstname         = str_replace($badcharacters,"",($cust_result["first_name"]));
			$lastname          = str_replace($badcharacters,"",($cust_result["last_name"]));
			$name              = $firstname.' '.$lastname;
			$company           = str_replace($badcharacters,"",($cust_result["company_name"]));
			$phone             = str_replace($badcharacters,"",($cust_result["addresses"][0]["phone"]));
			$email             = str_replace($badcharacters,"",($cust_result["addresses"][0]["email"]));
			$cardholder        = str_replace($badcharacters,"",($pay_result["card"]["name_on_card"]));
			$cc_last_4         = $pay_result["card"]["last_4_account_number"];
			$card_type         = $pay_result["card"]["card_type"];
			$expire_mo         = $pay_result["card"]["expire_month"];
			$expire_yr         = $pay_result["card"]["expire_year"];
			$accountholder     = str_replace($badcharacters,"",($pay_result["echeck"]["account_holder"]));
			$account_type      = $pay_result["echeck"]["account_type"];
			$routing           = $pay_result["echeck"]["routing_number"];
			$ach_last_4        = $pay_result["echeck"]["last_4_account_number"];
			$sched_start       = $sched_result["schedule_start_date"];
			$sched_status      = $sched_result["schedule_status"];
			$frequency         = $sched_result["schedule_frequency"];
			$total_pmts        = $sched_result["schedule_quantity"];
			$remaining_pmts    = $sched_result["schedule_summary"]["schedule_remaining_quantity"];
			$principal_amt     = $sched_result["schedule_amount"];
			$next_pmt_date     = $sched_result["schedule_summary"]["schedule_next_date"];
			$final_pmt_date    = $sched_result["schedule_summary"]["schedule_last_date"];
			$total_remaining   = $sched_result["schedule_summary"]["schedule_remaining_authorization_amount"];
			$no_success_pmts   = $sched_result["schedule_summary"]["schedule_successful_quantity"];
			$no_failed_pmts    = $sched_result["schedule_summary"]["schedule_failed_quantity"];
			$service_fee       = $sched_result["schedule_service_fee_amount"];
			$tax               = $sched_result["schedule_tax_amount"];

			$total_amount      = ($principal_amt + $service_fee + $tax);
			$sched_start1 = date('Y-m-d', strtotime($sched_start));
			$next_pmt_date1 = date('Y-m-d', strtotime($next_pmt_date));
			$final_pmt_date1 = date('Y-m-d', strtotime($final_pmt_date));
			
			// define the row
			$entries = $customer_id.','.$name.','.$company.','.$phone.','.$email.','.$sched_status.','.$sched_start1.','.$frequency.','.$next_pmt_date1.','.$final_pmt_date1.','.$total_pmts.','.$remaining_pmts.','.$no_success_pmts.','.$no_failed_pmts.','.$principal_amt.','.$service_fee.','.$total_amount.','.$total_remaining.PHP_EOL;

			//write the row
			fwrite($newfile,$entries);
			$count++;
		}
	}
};
fclose($cust_handle);
fclose($pay_handle);
fclose($sched_handle);

unlink("customer.list.csv");
unlink("paymethod.list.csv");
unlink("schedule.list.csv");
ob_end_clean();

//when export is finished, alert message
$message = "TOOLBOX 1 says:\\n\\nAll $count schedules exported successfully.\\n\\nFind the export file in the /toolbox1/importer/ folder as \"$filename\".";
echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";

// download it to the user's hardrive
$filesize = filesize('../'.$filename);
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Length: " . $filesize);
$fp = fopen('../'.$filename, "r");
fpassthru($fp);

?>