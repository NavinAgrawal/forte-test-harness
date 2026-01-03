<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script exports ALL records - CC first, then ACH

// disable timeout and notices
ini_set('max_execution_time', 0);
//error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('America/Chicago');

/* $base_url          = $_POST['base_url'];
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key'); */

$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$filename    = 'EXPORT.CUSTOMERS--MID.'.$merchant_id.'--'.date("Y.m.d").'.csv';

ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000';
$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';

$customer_set = "customer.list.csv";
$c = 0;
$rowCount = 0;

//Do a GET call and create a csv of customer tokens
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
	$rowcount = 0;
	$row = $rowcount;
	
	/* if ($customers_number_results == 0) {
		$customersDeleteCount = 0;
		goto goToPaymethods;
	}  */
	
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
		$cust_token = ($data["results"][$i]["customer_token"]);
		$sweet = fopen($customer_set, "a+");
		fwrite($sweet, $cust_token . PHP_EOL);
		$rowcount++;
		$row++;
	}
	
	// if the word "status" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "status";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		elseif($resultcount >= 1000){
			$c++;
		}
};





$customersDeleteCount = 0;
$scheduleCount = 0;

// define the header row
$headers = '"Merchant ID","Customer Token","Paymethod Token","Consumer ID","Status","First Name","Last Name","Company Name","Address 1","Address 2",City,State,Zipcode,Phone,Email,"Created On"'.PHP_EOL;

// write the header row
$newfile = fopen('../../../internal-toolbox/importer/'.$filename,"w+");
fwrite($newfile, $headers);

//loop thru the list created above and GET the customer info
if (file_exists($customer_set)) {
	$token = NULL;	
	$row = 1;
	if (($handle = fopen("customer.list.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
			$row++;
			for ($c=0; $c < $num; $c++) {
				$token = $data[$c];
				$endpoint3 = $endpoint2 . $token;
				$ch = curl_init($endpoint3);
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
				$response = curl_exec($ch);
				$info = curl_getinfo($ch);
				curl_close($ch);
				$data = json_decode($response,true);

				
				//strip away these characters
				$badcharacters = array(',','"',"'",'~','!','#','$','%','^','*','(',')');
				
				// define the fields
				$merchant_id     = str_replace("loc_","",($data["results"]["location_id"]));
				$customer_token  = str_replace("cst_","",($data["results"]["customer_token"]));
				$paymethod_token = str_replace("mth_","",($data["results"]["default_paymethod_token"]));
				$customer_id     = $data["results"]["customer_id"];
				$status          = $data["results"]["status"];
				$firstname       = str_replace($badcharacters,"",($data["results"]["first_name"]));
				$lastname        = str_replace($badcharacters,"",($data["results"]["last_name"]));
				$company         = str_replace($badcharacters,"",($data["results"]["company_name"]));
				$address1        = str_replace($badcharacters,"",($data["results"]["addresses"][0]["physical_address"]["street_line1"]));
				$address2        = str_replace($badcharacters,"",($data["results"]["addresses"][0]["physical_address"]["street_line2"]));
				$city            = str_replace($badcharacters,"",($data["results"]["addresses"][0]["physical_address"]["locality"]));
				$state           = str_replace($badcharacters,"",($data["results"]["addresses"][0]["physical_address"]["region"]));
				$zipcode         = str_replace($badcharacters,"",($data["results"]["addresses"][0]["physical_address"]["postal_code"]));
				$phone           = str_replace($badcharacters,"",($data["results"]["addresses"][0]["phone"]));
				$email           = str_replace($badcharacters,"",($data["results"]["addresses"][0]["email"]));
				$created         = $data["results"][$i]["created_date"];
				$created_date    = date('Y-m-d g:i A', strtotime("+2 hours $created"));
				
				echo $customer_id.' '.$status;
				exit;
				
				// define the row
				$entries = $merchant_id.','.$customer_token.','.$paymethod_token.','.$customer_id.','.$status.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$created_date.PHP_EOL;

				//write the row
				fwrite($newfile,$entries);
				//echo 'Writing customer '. $count . '<br>';
				$count++;				
				
				
			}
			
			
		
			
			
		}
		fclose($handle);
	}
	$remaining = $customers_number_results - $customersDeleteCount;
};
/*

/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////



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
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
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
		$counter         = $data["results"][$i]["paymethod"]["notes"];
		$created         = $data["results"][$i]["created_date"];

		$created_date = date('Y-m-d g:i A', strtotime("+2 hours $created"));
		
		// pad expire_month with leading zero if needed
		if(!empty($expire_mo)) {
			$new_mo = str_pad($expire_mo, 2, '0', STR_PAD_LEFT);
		}
			else {
				$new_mo = NULL;
			}
		
		// define the row
		$entries = $merchant_id.','.$customer_token.','.$paymethod_token.','.$customer_id.','.$status.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$cardholder.','.$cc_last_4.','.$card_type.','.$new_mo.','.$expire_yr.','.$accountholder.','.$account_type.','.$routing.','.$ach_last_4.','.$created_date.PHP_EOL;

		//write the row
		fwrite($newfile,$entries);
		//echo 'Writing customer '. $count . '<br>';
		$count++;
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


/////////////////////////////////////////////////////////
/////////////////   ECHECKS ONLY   //////////////////////
/////////////////////////////////////////////////////////

// begin the loop and do the GET call
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?filter=default_paymethod_type+eq+echeck&page_size=1000&orderby=customer_token';
$c = 0;
$count = 1;
while($c >= 0){	
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
	
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message')) window.history.back();</script>";
		exit;
	}

	// begin looping thru the GET call dataset
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
		//strip away these characters
		$badcharacters = array(',','"');
		
		// define the variables
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
		$counter         = $data["results"][$i]["paymethod"]["notes"];
		$created         = $data["results"][$i]["created_date"];

		$created_date = date('Y-m-d g:i A', strtotime("+2 hours $created"));
		
		// pad expire_month with leading zero if needed
		if(!empty($expire_mo)) {
			$new_mo = str_pad($expire_mo, 2, '0', STR_PAD_LEFT);
		}
			else {
				$new_mo = NULL;
			}
		
		// define the row
		$entries = $merchant_id.','.$customer_token.','.$paymethod_token.','.$customer_id.','.$status.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$cardholder.','.$cc_last_4.','.$card_type.','.$new_mo.','.$expire_yr.','.$accountholder.','.$account_type.','.$routing.','.$ach_last_4.','.$created_date.PHP_EOL;

		//write the row
		fwrite($newfile,$entries);
		$count++;
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


*/
?>