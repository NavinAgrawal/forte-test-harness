<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
//this script imports the ACH data

date_default_timezone_set('America/Chicago');
ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
array_map("unlink", glob("../failure.ACH.data.csv"));
array_map("unlink", glob("../failure.ACH.log.txt"));

rename ("../undo.import.ACH.csv","../undo.import.ACH.OLD.csv");

echo date("m/j/Y g:i:s a T");
echo '<br/><br/>';

//my sandbox
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';

//capture these variables from the importer engine
/* $endpoint        = $_GET['endpoint'];
$auth_token      = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url        = $_GET['base_url'];
$location_id     = $_GET['location_id'];  */

//set a few counters
$error_no = 1;
$success  = 0;
$fail     = 0;
$count    = 1;

// define the header row of the tokens file
$headers = '"Customer Token","Paymethod Token","Counter","First Name","Last Name","Company Name","Address 1","Address 2","City","State","Zipcode","Phone","Email","Customer ID","Account Holder","Routing No","Last 4","Account Type"'.PHP_EOL;

// create the tokens file and write the header row
$tokens = fopen("../tokens.ACH.csv", "w+");
$undo_import = fopen("../undo.import.ACH.csv", "w+");
fwrite($tokens, $headers);

//set the curl headers
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept:application/json',
	'Content-type: application/json'
));

//open the data file and initiate the import loop
$handle = fopen("../data.ACH.csv", "r");
while ($fields = fgetcsv($handle, 10000, ",")) {
	
	//define the dataset fields
	$counter        = $fields[0];
	$firstname      = $fields[1];
	$lastname       = $fields[2];
	$companyname    = $fields[3];
	$address1       = $fields[4];
	$address2       = $fields[5];
	$city           = $fields[6];
	$state          = $fields[7];
	$zipcode        = $fields[8];
	$phone          = $fields[9];
	$email          = $fields[10];
	$customer_id    = $fields[11];
	$account_holder = $fields[12];
	$routing_number = $fields[13];
	$account_number = $fields[14];
	$checking_type  = $fields[15];

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
				'company_name' => $companyname,
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
			'echeck' => array (
				'account_holder' => $account_holder,
				'routing_number' => $routing_number,
				'account_number' => $account_number,
				'account_type' => $checking_type,
			),
		)
	);
	
	//post the customer and paymethod objects
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

	//execute the curl session and capture the server response
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);
	$data = json_decode($response,true);

	echo '<pre>';
	echo date("h:i:s a");
	echo '<br/>';
	echo $count;
	echo '<br/>';
	print_r('HttpStatusCode: ' . $info['http_code']);
	echo '<br/>';
	print_r($data);
	$count++;
		
	//if the REST creds are wrong, alert message
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
		exit;
	}
	
	//if the failure log does not exist, create it and write the header
	if (!file_exists('../failure.ACH.log.txt')) {
		$fail_log = fopen("../failure.ACH.log.txt", "a+");
		fwrite($fail_log,"
////////////////////////////////////////////////////////////////
//////////////////                         /////////////////////
//////////////////   A C H   E R R O R S   /////////////////////
//////////////////                         /////////////////////
////////////////////////////////////////////////////////////////

");
		$log_stamp = date('n/j/Y g:i A T');
		fwrite($fail_log, $log_stamp . "\r\n");
		fwrite($fail_log, "========================\r\n");		
	}
	
	//create the failure data file
	$fail_data = fopen("../failure.ACH.data.csv", "a+");
	
	//if customer create fails, write it to the failure files
	if($info['http_code']==400) {
		$result = json_decode($response);
		$error = ($result->response->response_desc);
		fputcsv($fail_data, $fields);
		fwrite($fail_log, 'Line ' . $error_no . "\r\n");
		fwrite($fail_log, $error ."\r\n\r\n");
		$error_no++;
		$fail++;
		$count++;
	}
	
	//if create is successful, write the record (with tokens) to the tokens file
	if($info['http_code']==201) {
		$success++;
		$count++;
		$fields[14] = substr($fields[14],-4);  //truncate account number to last 4 only
		$customer_token  = str_replace("cst_","",($data["customer_token"]));
		$paymethod_token = str_replace("mth_","",($data["default_paymethod_token"]));
		fwrite($tokens, $customer_token.','.$paymethod_token.',');
		fputcsv($tokens, $fields);
		fwrite($undo_import, $customer_token . PHP_EOL);
	}
	//sleep(5);
}

curl_close($ch);

fclose($handle);
fclose($fail_data);
fclose($fail_log);
fclose($tokens);
fclose($undo_import);
//unlink('../data.ACH.csv');

//when import is finished, alert message
if($fail == 0) {
	unlink("../failure.ACH.data.csv");
	unlink("../failure.ACH.log.txt");
	$message = "TOOLBOX 1 says:\\n\\nCongratulations! No failures.\\nAll $success ACH customers imported successfully.";
	echo "<script type='text/javascript'>alert('$message');</script>";
	} 
  else {
	$message = "TOOLBOX 1 says:\\n\\n$success ACH customers imported successfully.\\n$fail failures.";
	echo "<script type='text/javascript'>alert('$message');</script>";
  }
echo '<br/><br/>';
echo date("h:i:sa");
?>