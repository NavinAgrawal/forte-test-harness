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

////////////////////////////////////////////////////////////////////// */

$base_url          = forte_base_url();             //production
//$base_url          = forte_base_url();     //sandbox
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$token             = NULL;
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
	
$row = 1;
if (($handle = fopen("tokens04.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $num = count($data);
    echo "<p> $num fields in line $row: <br /></p>\n";
    $row++;
    for ($c=0; $c < $num; $c++) {
		$token = $data[$c];
		$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/'.'cst_'.$token;
		
		$ch = curl_init($endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');                //POST, GET, PUT or DELETE (Create, Find, Update or Delete)
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Basic ' . $auth_token,
			'X-Forte-Auth-Organization-id: ' . $organization_id,
			'Accept:application/json',
			'Content-type: application/json'
	));

	$response = curl_exec($ch);		
		
    echo $data[$c] . "<br />\n";
	$info = curl_getinfo($ch);
	curl_close($ch);
	$data = json_decode($response);

	echo '<pre>';
	print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
	print_r($data);
	echo '</pre>';
    }
  }
  fclose($handle);
}

?>