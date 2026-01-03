<?php
//this script imports the CC data

date_default_timezone_set('America/Chicago');
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE);
array_map("unlink", glob("../../../toolbox1/importer/failure.CC.data.csv"));
array_map("unlink", glob("../../../toolbox1/importer/failure.CC.log.txt"));

//capture these variables from the importer engine
$endpoint        = $_GET['endpoint'];
$auth_token      = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url        = $_GET['base_url'];
$location_id     = $_GET['location_id'];

//set a few counters
$error_no = 1;
$success  = 0;
$fail     = 0;
$count    = 1;
$throttle = 1;

// define the header row
$headers = '"Customer Token","Paymethod Token","Counter","First Name","Last Name","Company Name","Address 1","Address 2",City,"State","Zipcode","Phone","Email","Customer ID","Last 4","Cardholder Name","Card Type","Expire Mo","Expire Year"'.PHP_EOL;

// open the tokens file and write the header row
$tokens = fopen("../../../toolbox1/importer/tokens.CC.csv", "a+");
fwrite($tokens, $headers);

//open the data file and initiate the import loop
$handle = fopen("../../../toolbox1/importer/data.csv", "r");
while ($fields = fgetcsv($handle, 10000, ",")) {

	//define the dataset fields
	$counter     = $fields[0];
	$firstname   = $fields[1];
	$lastname    = $fields[2];
	$companyname = $fields[3];
	$address1    = $fields[4];
	$address2    = $fields[5];
	$city        = $fields[6];
	$state       = $fields[7];
	$zipcode     = $fields[8];
	$phone       = $fields[9];
	$email       = $fields[10];
	$customer_id = $fields[11];
	$cardnumber  = $fields[12];
	$cardholder  = $fields[13];
	$cardtype    = $fields[14];
	$expireMonth = $fields[15];
	$expireYear  = $fields[16];

	//define the customer and paymethod parameters
	$params = array (
		'first_name' => $firstname,
		'last_name' => $lastname,
		'company_name' => $companyname,
		'customer_id' => $customer_id,
		'addresses' => array ( 
			array (
				'first_name' => $firstname,
				'last_name' => $lastname,
				'email' => $email,
				'phone' => $phone,
				'address_type' => 'default_billing',
				'physical_address' => array (
					'street_line1' => $address1,
					'street_line2' => $address2,
					'locality' => $city,
					'region' => $state,
					'postal_code' => $zipcode
				)
			)
		),
		'paymethod' => array (
			'notes' => $counter,
			'card' => array (
				'card_type' => $cardtype,
				'name_on_card' => $cardholder,
				'account_number' => $cardnumber,
				'expire_month' => sprintf("%02d",$expireMonth),   //give single-digit months a leading zero
				'expire_year' => $expireYear
			),
		)
	);

	//declare the curl headers
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

	//execute the curl session and capture the server response
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	$data = json_decode($response,true);
	
	//if REST creds are wrong, alert message
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}
	
	//if the failure log does not exist, create it and write the header
	if (!file_exists('../../../toolbox1/importer/failure.CC.log.txt')) {
		$fail_log = fopen("../../../toolbox1/importer/failure.CC.log.txt", "a+");
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
	$fail_data = fopen("../../../toolbox1/importer/failure.CC.data.csv", "a+");
	
	//if customer create fails, write it to the failure files
	if($info['http_code']==400) {
		$result = json_decode($response);
		$error = ($result->response->response_desc);
		fputcsv($fail_data, $fields);
		fwrite($fail_log, 'Line ' . $error_no . "\r\n");
		fwrite($fail_log, $error ."\r\n\r\n");
		$error_no++;
		$fail++;
	}
	
	//if create is successful, write the record (with tokens) to the tokens file
	if($info['http_code']==201) {
		$success++;
		$count++;
		$fields[12] = substr($fields[12],-4);  //convert card number to last 4 only	
		$customer_token  = str_replace("cst_","",($data["customer_token"]));
		$paymethod_token = str_replace("mth_","",($data["default_paymethod_token"]));
		fwrite($tokens, $customer_token.','.$paymethod_token.',');
		fputcsv($tokens, $fields);
	}
}

fclose($handle);
fclose($fail_data);
fclose($fail_log);
fclose($tokens);

//when import is finished, alert message
if($fail == 0) {
	unlink("../../../toolbox1/importer/failure.CC.data.csv");
	unlink("../../../toolbox1/importer/failure.CC.log.txt");
	$message = "TOOLBOX 1:\\n\\nCongratulations! No failures.\\nAll $success records imported successfully.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
  else {
	$message = "TOOLBOX 1:\\n\\n$success customers imported successfully.\\n$fail failures.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
  }
?>