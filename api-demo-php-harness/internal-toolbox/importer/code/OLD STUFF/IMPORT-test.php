<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script exports all of the customers with "card" as default_paymethod_type

// disable timeout and notices
ini_set('max_execution_time', 0);
//error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('America/Chicago');
unlink('paymethod.tokens.csv');

$base_url          = $_POST['base_url'];
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');

$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$filename    = 'EXPORT.CC.CUSTOMERS--MID.'.$merchant_id.'--'.date("Y.m.d").'.csv';
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?page_size=1000&orderby=paymethod_token';
$endpoint2   = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/';
$token_set   = "paymethod.tokens.csv";

// define the header row
$headers = '"Merchant ID","Customer Token","Paymethod Token","Consumer ID","Status","First Name","Last Name","Company Name","Address 1","Address 2",City,State,Zipcode,Phone,Email,"Cardholder Name","Last 4","Card Type","Expire Mo","Expire Yr","Created On"'.PHP_EOL;

// write the header row
$newfile = fopen('../../../internal-toolbox/importer/'.$filename,"w+");
fwrite($newfile, $headers);

$c = 0;
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
	$number_results = $data["number_results"];

	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	if($number_results == 0) {
		$message = "There are no customers to export.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}	



	
	for($i=0; $i < sizeof($data["results"]); $i++) {
		$pay_token = ($data["results"][$i]["paymethod_token"]);
		$sweet = fopen($token_set, "a+");
		
		if (isset($data["results"][$i]["customer_token"])) {
			goto nextRecord;
		}
		
		fwrite($sweet, $pay_token . PHP_EOL);
		nextRecord:
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
}
//loop thru the list created above and write the customer to the csv file
$handle = fopen("paymethod.tokens.csv", "r");
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	$num = count($data);
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
		$badcharacters = array(',','"');
		
		// define the fields
		$merchant_id     = str_replace("loc_","",($data["location_id"]));
		$customer_token  = str_replace("cst_","",($data["customer_token"]));
		$paymethod_token = str_replace("mth_","",($data["default_paymethod_token"]));
		$customer_id     = $data["customer_id"];
		$status          = $data["status"];
		$firstname       = str_replace($badcharacters,"",($data["first_name"]));
		$lastname        = str_replace($badcharacters,"",($data["last_name"]));
		$company         = str_replace($badcharacters,"",($data["company_name"]));
		$address1        = str_replace($badcharacters,"",($data["addresses"][0]["physical_address"]["street_line1"]));
		$address2        = str_replace($badcharacters,"",($data["addresses"][0]["physical_address"]["street_line2"]));
		$city            = str_replace($badcharacters,"",($data["addresses"][0]["physical_address"]["locality"]));
		$state           = str_replace($badcharacters,"",($data["addresses"][0]["physical_address"]["region"]));
		$zipcode         = str_replace($badcharacters,"",($data["addresses"][0]["physical_address"]["postal_code"]));
		$phone           = str_replace($badcharacters,"",($data["addresses"][0]["phone"]));
		$email           = str_replace($badcharacters,"",($data["addresses"][0]["email"]));
		$cardholder      = str_replace($badcharacters,"",($data["card"]["name_on_card"]));
		$cc_last_4       = $data["card"]["last_4_account_number"];
		$card_type       = $data["paymethod"]["card"]["card_type"];
		$expire_mo       = sprintf("%02d",($data["paymethod"]["card"]["expire_month"]));
		$expire_yr       = $data["paymethod"]["card"]["expire_year"];
		$accountholder   = str_replace($badcharacters,"",($data["paymethod"]["echeck"]["account_holder"]));
		$account_type    = $data["paymethod"]["echeck"]["account_type"];
		$routing         = $data["paymethod"]["echeck"]["routing_number"];
		$ach_last_4      = $data["paymethod"]["echeck"]["last_4_account_number"];
		$counter         = $data["paymethod"]["notes"];
		$created         = $data["created_date"];
		$created_date    = date('Y-m-d g:i A', strtotime("+2 hours $created"));
		
		// define the row
		$entries = $merchant_id.','.$customer_token.','.$paymethod_token.','.$customer_id.','.$status.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$cardholder.','.$cc_last_4.','.$card_type.','.$expire_mo.','.$expire_yr.','.$created_date.PHP_EOL;

		//write the row
		fwrite($newfile,$entries);
		$count++;				
	}
}

fclose($handle);
fclose($sweet);
unlink('paymethod.tokens.csv');
ob_end_clean();

// download it to the user's hardrive
$filesize = filesize('../../../internal-toolbox/importer/'.$filename);
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Length: " . $filesize);
$fp = fopen('../../../internal-toolbox/importer/'.$filename, "r");
fpassthru($fp);

?>