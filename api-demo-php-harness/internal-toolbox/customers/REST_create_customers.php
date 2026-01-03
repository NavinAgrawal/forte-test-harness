<?php
require_once __DIR__ . '/../../config/bootstrap.php';
/*
REST Developer Documentation:       https://www.forte.net/devdocs/api_resources/forte_api_v3.htm
Best Practices for Payment Forms:   https://www.forte.net/devdocs/reference/payment_forms.htm
Transaction Response Codes:         https://www.forte.net/devdocs/reference/response_codes.htm
Frequently Asked Questions:         https://www.forte.net/devdocs/reference/faq.htm
Forte Technical Support:
			7:00 am - 7:00 pm CST
			866.290.5400 option 5
			integration@forte.net
*/

include 'randomCustomerGenerator.php';
ini_set('max_execution_time', 0);

//$base_url          = forte_base_url();             //production
//$base_url          = forte_base_url();     //sandbox
//$organization_id   = forte_config('organization_id');
//$location_id       = forte_config('location_id');
//$api_access_id     = forte_config('api_access_id');
//$api_secure_key    = forte_config('api_secure_key');
//$token             = NULL;
//$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);

$cg = new Customer_Generator();
$results = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$genNum	 = sanitize_input($_POST['genNum'], "Enter your name");
$apiID 	 = sanitize_input($_POST['apiID'], "Write a subject");
$apiKey  = sanitize_input($_POST['apiKey']);
$orgID   = sanitize_input($_POST['orgID']);
$locID 	 = sanitize_input($_POST['locID']);
$baseURL = sanitize_input($_POST['base_url']);

}

//Uncomment to disable hard coded testing mode
$base_url          = $baseURL ?: forte_base_url();
$organization_id   = forte_prefixed_post('orgID', 'org_', 'organization_id');
$location_id       = forte_prefixed_post('locID', 'loc_', 'location_id');
$api_access_id     = forte_post_value('apiID', 'api_access_id');
$api_secure_key    = forte_post_value('apiKey', 'api_secure_key');
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);

function sanitize_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);

  return $data;
}
echo $genNum;

for ($i = 0; $i < $genNum;$i++) {
		
	$new_cust = $cg->create_random_customer();
	
	//var_dump($new_cust);
	$json_string = json_encode($new_cust->export_to_array());

	//$row = 1;
	//if (($handle = fopen("tokens04.csv", "r")) !== FALSE) {
	//  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	//    $num = count($data);
	//    echo "<p> $num fields in line $row: <br /></p>\n";
	//    $row++;
	//    for ($c=0; $c < $num; $c++) {
	//		$token = $data[$c];
			$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
			
			$ch = curl_init($endpoint);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');                //POST, GET, PUT or DELETE (Create, Find, Update or Delete)
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Basic ' . $auth_token,
				'X-Forte-Auth-Organization-id: ' . $organization_id,
				'Accept:application/json',
				'Content-type: application/json'
		));

		$response = curl_exec($ch);		
			
	//    echo $data[$c] . "<br />\n";
		$info = curl_getinfo($ch);
		curl_close($ch);
		$customer_result = json_decode($response);
		
		$new_customer = new Customer();
		$new_customer->hydrate($customer_result);
		$environment = $customer_result->response->environment;
		$response_desc = $customer_result->response->response_desc;
		
		echo '<pre>';
		print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
		print_r($customer_result);
		echo '</pre>';
}
//    }
//  }
//  fclose($handle);
//}

?>
