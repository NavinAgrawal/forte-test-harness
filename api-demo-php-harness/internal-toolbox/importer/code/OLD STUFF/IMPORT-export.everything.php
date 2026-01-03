<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script exports all customers and schedules

date_default_timezone_set('America/Chicago');

$base_url          = $_POST['base_url'];
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');

/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */

/* $base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');  */

$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$filename    = 'EXPORT.EVERYTHING--MID.'.$merchant_id.'--'.date("Y.m.d").'.csv';

// disable timeout and notices
ini_set('max_execution_time', 0);
//error_reporting(E_ALL & ~E_NOTICE);

// define the header row
$headers = '"Merchant ID","Consumer ID","Customer Token","Paymethod Token","First Name","Last Name","Company Name","Address 1","Address 2","City","State","Zipcode","Phone","Email","Cardholder Name","Card Type","Last 4","Expire Mo","Expire Yr","Accountholder","Account Type","Routing","Last 4","Schedule Status","Start Date","Frequency","Next Payment","Final Payment","Total Pmts","Remaining Pmts","Successful Pmts","Failed Pmts","Payment Amt","Remaining Balance"'.PHP_EOL;

// write the header row
$newfile = fopen('../../../internal-toolbox/importer/'.$filename,"w+");
fwrite($newfile, $headers);

//GET on /customers endpoint
// begin the loop and do the GET call
$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000';
$c = 0;

$count = 1;
while($c >= 0){
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000';
	$ch = curl_init($endpoint.'&page_index='.$c);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Basic ' . $auth_token,
		'X-Forte-Auth-Organization-id: ' . $organization_id,
		'Accept:application/json'
	));
	
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	$data = json_decode($response,true);
	$number_results = $data["number_results"];
	
	if($info['http_code'] == 401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	if($number_results == 0) {
		$message = "There are no customers to export.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	// begin looping thru the GET call dataset
	for($i=0; $i < sizeof($data["results"]); $i++) {
		//strip away these characters
		$badcharacters = array(',','"',"'",'~','!','#','$','%','^','*','(',')');
		
		// define the fields
		$merchant_id     = str_replace("loc_","",($data["results"][$i]["location_id"]));
		$customer_token  = str_replace("cst_","",($data["results"][$i]["customer_token"]));
		$paymethod_token = str_replace("mth_","",($data["results"][$i]["default_paymethod_token"]));
		$customer_id     = $data["results"][$i]["customer_id"];
		$status          = $data["results"][$i]["status"];
		$firstname       = str_replace($badcharacters,"",($data["results"][$i]["first_name"]));
		$lastname        = str_replace($badcharacters,"",($data["results"][$i]["last_name"]));
		$company         = str_replace($badcharacters,"",($data["results"][$i]["company_name"]));
		$address1        = str_replace($badcharacters,"",($data["results"][$i]["addresses"][0]["physical_address"]["street_line1"]));
		$address2        = str_replace($badcharacters,"",($data["results"][$i]["addresses"][0]["physical_address"]["street_line2"]));
		$city            = str_replace($badcharacters,"",($data["results"][$i]["addresses"][0]["physical_address"]["locality"]));
		$state           = str_replace($badcharacters,"",($data["results"][$i]["addresses"][0]["physical_address"]["region"]));
		$zipcode         = str_replace($badcharacters,"",($data["results"][$i]["addresses"][0]["physical_address"]["postal_code"]));
		$phone           = str_replace($badcharacters,"",($data["results"][$i]["addresses"][0]["phone"]));
		$email           = str_replace($badcharacters,"",($data["results"][$i]["addresses"][0]["email"]));
		$cardholder      = str_replace($badcharacters,"",($data["results"][$i]["paymethod"]["card"]["name_on_card"]));
		$cc_last_4       = $data["results"][$i]["paymethod"]["card"]["last_4_account_number"];
		$card_type       = $data["results"][$i]["paymethod"]["card"]["card_type"];
		$expire_mo       = $data["results"][$i]["paymethod"]["card"]["expire_month"];
		$expire_yr       = $data["results"][$i]["paymethod"]["card"]["expire_year"];
		$accountholder   = str_replace($badcharacters,"",($data["results"][$i]["paymethod"]["echeck"]["account_holder"]));
		$account_type    = $data["results"][$i]["paymethod"]["echeck"]["account_type"];
		$routing         = $data["results"][$i]["paymethod"]["echeck"]["routing_number"];
		$ach_last_4      = $data["results"][$i]["paymethod"]["echeck"]["last_4_account_number"];
		$created         = $data["results"][$i]["created_date"];

		$created_date = date('Y-m-d g:i A', strtotime("+2 hours $created"));
		
		// pad expire_month with leading zero if needed
		if(!empty($expire_mo)) {
			$new_mo = str_pad($expire_mo, 2, '0', STR_PAD_LEFT);
		}
			else {
				$new_mo = NULL;
			}
		// write the csv file
		
		// define the row
		//$entries = $merchant_id.','.$customer_id.','.$customer_token.','.$paymethod_token.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$cardholder.','.$card_type.','.$cc_last_4.','.$expire_mo.','.$expire_yr.','.$accountholder.','.$account_type.','.$routing.','.$ach_last_4.PHP_EOL;

		//write the row
		//fwrite($newfile,$entries);			

		$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/cst_'.$customer_token.'/schedules';
		$ch = curl_init($endpoint2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Basic ' . $auth_token,
			'X-Forte-Auth-Organization-id: ' . $organization_id,
			'Accept:application/json'
		));
		
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		$sched_result = json_decode($response,true);
		$number_results = $sched_result["number_results"];
		
 		if($sched_result["results"] == NULL) {
			goto skipit;
		}
		
		$sched_start       = $sched_result["results"][0]["schedule_start_date"];
		$sched_status      = $sched_result["results"][0]["schedule_status"];
		$frequency         = $sched_result["results"][0]["schedule_frequency"];
		$total_pmts        = $sched_result["results"][0]["schedule_quantity"];
		$remaining_pmts    = $sched_result["results"][0]["schedule_summary"]["schedule_remaining_quantity"];
		$principal_amt     = $sched_result["results"][0]["schedule_summary"]["schedule_next_amount"];
		$next_pmt_date     = $sched_result["results"][0]["schedule_summary"]["schedule_next_date"];
		$final_pmt_date    = $sched_result["results"][0]["schedule_summary"]["schedule_last_date"];
		$total_remaining   = $sched_result["results"][0]["schedule_summary"]["schedule_remaining_amount"];
		$no_success_pmts   = $sched_result["results"][0]["schedule_summary"]["schedule_successful_quantity"];
		$no_failed_pmts    = $sched_result["results"][0]["schedule_summary"]["schedule_failed_quantity"];
		//$service_fee       = $sched_result["results"][0]["schedule_service_fee_amount"];

		$total_amount      = ($principal_amt + $service_fee);
		$sched_start1 = date('Y-m-d', strtotime($sched_start));
		$next_pmt_date1 = date('Y-m-d', strtotime($next_pmt_date));
		$final_pmt_date1 = date('Y-m-d', strtotime($final_pmt_date));
		
/*		if($total_pmts == 0) {
			$total_pmts = "continuous";
			$remaining_pmts = "continuous";
		}  */
		
		skipit:
		
		// define the row
		$entries = $merchant_id.','.$customer_id.','.$customer_token.','.$paymethod_token.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$cardholder.','.$card_type.','.$cc_last_4.','.$expire_mo.','.$expire_yr.','.$accountholder.','.$account_type.','.$routing.','.$ach_last_4.','.$sched_status.','.$sched_start1.','.$frequency.','.$next_pmt_date1.','.$final_pmt_date1.','.$total_pmts.','.$remaining_pmts.','.$no_success_pmts.','.$no_failed_pmts.','.$total_amount.','.$total_remaining.PHP_EOL;

		//write the row
		fwrite($newfile,$entries);
		
		$sched_start       = NULL;
		$sched_status      = NULL;
		$frequency         = NULL;
		$total_pmts        = NULL;
		$remaining_pmts    = NULL;
		$principal_amt     = NULL;
		$next_pmt_date     = NULL;
		$final_pmt_date    = NULL;
		$total_remaining   = NULL;
		$no_success_pmts   = NULL;
		$no_failed_pmts    = NULL;
		
	}
	
	// if the string "status" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "status";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		else if($resultcount >= 1000){
			$c++;
		}
};

// download it to the user's hardrive
$filesize = filesize('../../../internal-toolbox/importer/'.$filename);
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Length: " . $filesize);
$fp = fopen('../../../internal-toolbox/importer/'.$filename, "r");
fpassthru($fp);
?>