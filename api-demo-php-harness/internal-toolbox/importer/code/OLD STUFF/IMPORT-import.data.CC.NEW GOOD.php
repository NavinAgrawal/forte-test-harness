<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
//this script imports the CC data

date_default_timezone_set('America/Chicago');
ini_set('max_execution_time', 0);
array_map("unlink", glob("../../../internal-toolbox/importer/failure.CC.data.csv"));
array_map("unlink", glob("../../../internal-toolbox/importer/failure.CC.log.txt"));

//my sandbox
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);

/* $endpoint = $_GET['endpoint'];
$auth_token = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url = $_GET['base_url'];
$location_id = $_GET['location_id']; */

$error_no = 1;
$success = 0;
$fail = 0;

$tokens = fopen("../../../internal-toolbox/importer/tokens.CC.csv", "w+");

// define and write the header row
$headers = '"Merchant ID","Customer Token","Paymethod Token","Consumer ID","First Name","Last Name","Company Name","Address 1","Address 2",City,State,Zipcode,Phone,Email,"Cardholder Name","Last 4","Card Type","Expire Mo","Expire Yr"'.PHP_EOL;
fwrite($tokens, $headers);
		
$handle = fopen("../../../internal-toolbox/importer/data.csv", "r");
while ($fields = fgetcsv($handle, 1000, ",")) {
	//define the dataset variables
	$firstname   = $fields[0];
	$lastname    = $fields[1];
	$companyname = $fields[2];
	$address1    = $fields[3];
	$address2    = $fields[4];
	$city        = $fields[5];
	$state       = $fields[6];
	$zipcode     = $fields[7];
	$phone       = $fields[8];
	$email       = $fields[9];
	$customer_id = $fields[10];
	$cardnumber  = $fields[11];
	$cardholder  = $fields[12];
	$cardtype    = $fields[13];
	$expireMonth = $fields[14];
	$expireYear  = $fields[15];

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
			'card' => array (
				'card_type' => $cardtype,
				'name_on_card' => str_replace($badcharacters,"",$cardholder),
				'account_number' => $cardnumber,
				'expire_month' => sprintf("%02d",$expireMonth),
				'expire_year' => $expireYear
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
print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
print_r($data);
echo '</pre>';
	
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	//if the failure log does not exist, create it and write the header
	if (!file_exists('../../../internal-toolbox/importer/failure.CC.log.txt')) {
		$fail_log = fopen("../../../internal-toolbox/importer/failure.CC.log.txt", "a+");
		fwrite($fail_log, "
////////////////////////////////////////////////////////////////
////////////////////                       /////////////////////
////////////////////   C C   E R R O R S   /////////////////////
////////////////////                       /////////////////////
////////////////////////////////////////////////////////////////

");
		$log_stamp = date('n/j/Y g:i A T');
		fwrite($fail_log, $log_stamp . "\r\n");
		fwrite($fail_log, "========================\r\n");		
	}
	
	//create the failure data file
	$fail_data = fopen("../../../internal-toolbox/importer/failure.CC.data.csv", "a+");
	
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
	
	if($info['http_code']==201) {
		$success++;	

		//strip away these characters
		$badcharacters = array(',','"');
		
		// define the fields
		$merchant_id     = str_replace("loc_","",($data["location_id"]));
		$customer_token  = str_replace("cst_","",($data["customer_token"]));
		$paymethod_token = str_replace("mth_","",($data["default_paymethod_token"]));
		$customer_id     = $data["customer_id"];
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
		
		// pad expire_month with leading zero if needed
		if(!empty($expire_mo)) {
			$new_mo = str_pad($expire_mo, 2, '0', STR_PAD_LEFT);
		}
			else {
				$new_mo = NULL;
			}



		// define the row
		$entries = $merchant_id.','.$customer_token.','.$paymethod_token.','.$customer_id.','.$firstname.','.$lastname.','.$company.','.$address1.','.$address2.','.$city.','.$state.','.$zipcode.','.$phone.','.$email.','.$cardholder.','.$cc_last_4.','.$card_type.','.$new_mo.','.$expire_yr.PHP_EOL;

		//write the row
		fwrite($tokens,$entries);

	}
}

fclose($handle);
fclose($fail_data);
fclose($fail_log);
/* 
if($fail == 0) {
	fwrite($fail_log, 'Congratulations! No failures.' . "\r\n\r\n");
	unlink("../../../internal-toolbox/importer/failure.CC.data.csv");
	unlink("../../../internal-toolbox/importer/failure.CC.log.txt");
	$message = "Congratulations! No failures.\\nAll $success records imported successfully.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
  else {
	$message = "$success customers imported successfully.\\n$fail failures.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
  }  */
?>