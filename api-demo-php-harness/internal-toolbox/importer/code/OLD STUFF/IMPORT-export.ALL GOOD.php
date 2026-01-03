<?php
//this script exports all of the customers with "card" as default_paymethod_type

// disable timeout and notices
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('America/Chicago');
unlink('customer.tokens.csv');

$auth_token      = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url        = $_GET['base_url'];
$location_id     = $_GET['location_id'];

$merchant_id = str_replace("loc_","",$location_id);
$filename    = 'CUSTOMERS-'.date("Y.m.d").'.csv';
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000&orderby=customer_token';
$endpoint2   = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
$token_set   = "customer.tokens.csv";

// define the header row
$headers = '"Customer Token","Paymethod Token","Consumer ID","Status","First Name","Last Name","Company Name","Address 1","Address 2",City,State,Zipcode,Phone,Email,"Cardholder Name","Last 4","Card Type","Expire Mo","Expire Yr","Account Holder","Account Type","Routing No","Last 4"'.PHP_EOL;

// write the header row
$newfile = fopen('../../../toolbox1/importer/'.$filename,"w+");
fwrite($newfile, $headers);

$c = 0;
$count = 0;

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
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
		exit;
	}
	if($number_results == 0) {
		$message = "There are no customers to export.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
		exit;
	}	
	
	for($i=0; $i < sizeof($data["results"]); $i++) {
		$cust_token = ($data["results"][$i]["customer_token"]);
		$sweet = fopen($token_set, "a+");
		fwrite($sweet, $cust_token . PHP_EOL);
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
$handle = fopen("customer.tokens.csv", "r");
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
		$cardholder      = str_replace($badcharacters,"",($data["paymethod"]["card"]["name_on_card"]));
		$cc_last_4       = $data["paymethod"]["card"]["last_4_account_number"];
		$card_type       = $data["paymethod"]["card"]["card_type"];
		$expire_mo       = $data["paymethod"]["card"]["expire_month"];
		$expire_yr       = $data["paymethod"]["card"]["expire_year"];
		$accountholder   = str_replace($badcharacters,"",($data["paymethod"]["echeck"]["account_holder"]));
		$account_type    = $data["paymethod"]["echeck"]["account_type"];
		$routing         = $data["paymethod"]["echeck"]["routing_number"];
		$ach_last_4      = $data["paymethod"]["echeck"]["last_4_account_number"];
		$counter         = $data["paymethod"]["notes"];
		$created         = $data["created_date"];
		$created_date    = date('Y-m-d g:i A', strtotime("+2 hours $created"));
		
		// pad expire_month with leading zero if needed
		if(!empty($expire_mo)) {
			$new_mo = str_pad($expire_mo, 2, '0', STR_PAD_LEFT);
		}
			else {
				$new_mo = NULL;
			}

		// define the row
		$entries = $customer_token.','.$paymethod_token.','.$customer_id.','.$status.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$cardholder.','.$cc_last_4.','.$card_type.','.$new_mo.','.$expire_yr.','.$accountholder.','.$account_type.','.$routing.','.$ach_last_4.PHP_EOL;

		//write the row
		fwrite($newfile,$entries);
		$count++;				
	}
}

fclose($handle);
fclose($sweet);
unlink('customer.tokens.csv');
ob_end_clean();

//when export is finished, alert message
$message = "TOOLBOX 1 says:\\n\\nAll $count customers exported successfully.\\n\\nFind the export file in the /toolbox1/importer/ folder as \"$filename\".";
echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";

// download it to the user's hardrive
$filesize = filesize('../../../toolbox1/importer/'.$filename);
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Length: " . $filesize);
$fp = fopen('../../../toolbox1/importer/'.$filename, "r");
fpassthru($fp);

?>