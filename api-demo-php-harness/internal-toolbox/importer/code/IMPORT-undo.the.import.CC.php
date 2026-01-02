<?php
//this script undoes the CC customer import by looping thru the customer token list

$endpoint        = $_GET['endpoint'];
$endpoint2       = $_GET['endpoint2'];
$auth_token      = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url        = $_GET['base_url'];
$location_id     = $_GET['location_id'];

ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$c = 0;
$customersDeleteCount = 0;

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_HTTPHEADER, array (
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-id: ' . $organization_id,
	'Accept: application/json',
	'Content-type: application/json'
));

//loop thru the token file and delete the CC customers
$token = NULL;	
$handle = fopen("../undo.import.CC.csv", "r");
while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
	$num = count($data);
	for ($c=0; $c < $num; $c++) {
		$token = $data[$c];
		$endpoint3 = $endpoint2 . 'cst_' . $token;
		curl_setopt($ch, CURLOPT_URL, $endpoint3);

		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		//$data = json_decode($response,true);
		
		if($info['http_code']==200) {
			$customersDeleteCount++;
		}
	}
	sleep(4);
}

curl_close($ch);

fclose($handle);

if($customersDeleteCount > 0) {
	$message = "TOOLBOX 1 says:\\n\\nUndo import has been completed.\\n\\n$customersDeleteCount customers have been deleted.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
}
if($customersDeleteCount == 0) {
		$message = "TOOLBOX 1 says:\\n\\nNone of the existing customers matched up with the tokens file.\\n\\n$customersDeleteCount customers have been deleted.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	}

?>