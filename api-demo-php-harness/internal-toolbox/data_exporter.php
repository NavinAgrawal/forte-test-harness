<?php
require_once __DIR__ . '/../config/bootstrap.php';

$base_url          = $_POST['base_url'];
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');

$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$filename    = 'DATA.EXPORT--MID.'.$merchant_id.'--'.date("Y.m.d").'.csv';
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000&orderby=customer_token';

// disable timeout and notices
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE);

// define the header row
$headers = '"Merchant ID","Customer Token","Paymethod Token","Consumer ID","First Name","Last Name","Company Name","Address 1","Address 2",City,State,Zipcode,Phone,Email,"Cardholder Name","Last 4","Card Type","Expire Mo","Expire Yr","Account Holder","Account Type","Routing No","Last 4"'.PHP_EOL;

// write the header row
$newfile = fopen($filename,"w+");
fwrite($newfile, $headers);

// begin the loop and do the GET call
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
	curl_close($ch);
	$data = json_decode($response,true);
	$number_results = $data["number_results"];
	
	// begin looping thru the GET call dataset
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
		//strip away these characters
		$badcharacters = array(',','"');
		
		// define the fields
		$merchant_id     = str_replace("loc_","",($data["results"][$i]["location_id"]));
		$customer_token  = $data["results"][$i]["customer_token"];
		$paymethod_token = $data["results"][$i]["default_paymethod_token"];
		$customer_id     = $data["results"][$i]["customer_id"];
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
		$cc_last_4       = $data["results"][$i]["paymethod"]["card"]["masked_account_number"];
		$card_type       = $data["results"][$i]["paymethod"]["card"]["card_type"];
		$expire_mo       = $data["results"][$i]["paymethod"]["card"]["expire_month"];
		$expire_yr       = $data["results"][$i]["paymethod"]["card"]["expire_year"];
		$accountholder   = str_replace($badcharacters,"",($data["results"][$i]["paymethod"]["echeck"]["account_holder"]));
		$account_type    = $data["results"][$i]["paymethod"]["echeck"]["account_type"];
		$routing         = $data["results"][$i]["paymethod"]["echeck"]["routing_number"];
		$ach_last_4      = $data["results"][$i]["paymethod"]["echeck"]["masked_account_number"];
		$counter         = $data["results"][$i]["paymethod"]["notes"];
		
		// define the row
		$entries = $merchant_id.','.$customer_token.','.$paymethod_token.','.$customer_id.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$cardholder.','.$cc_last_4.','.$card_type.','.$expire_mo.','.$expire_yr.','.$accountholder.','.$account_type.','.$routing.','.$ach_last_4.PHP_EOL;

		//write the row
		fwrite($newfile,$entries);
		//echo 'Writing customer '. $count . '<br>';
		$count++;
	}
	
	// if the string "created_date" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "created_date";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		else if($resultcount >= 1000){
			$c++;
		}
};

$customers = $count-1;
$message = "$customers customers retrieved successfully.\\nFilename is $filename";
//echo "<script type='text/javascript'>confirm('$message');</script>";

// download it to the user's hardrive
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Length: " . filesize($filename));
$fp = fopen($filename, "r");
fpassthru($fp);
?>