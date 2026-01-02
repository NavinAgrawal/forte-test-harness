<?php
//this script deletes all existing customers. First it creates a csv file of the tokens, then loops thru the tokens.

ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?orderby=customer_token+asc&page_size=1000';
$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';

$delete_set = "customers.delete.csv";
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
	
	if ($customers_number_results == 0) {
		$customersDeleteCount = 0;
		goto goToPaymethods;
	}
	
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
		$cust_token = ($data["results"][$i]["customer_token"]);
		
		
		$endpoint3 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/'.$cust_token;
		$ch = curl_init($endpoint3);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
		echo '<pre>';
		print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
		print_r($data);
		echo '</pre>';
		
		
		
		
		//$sweet = fopen($delete_set, "a+");
		//fwrite($sweet, $cust_token . PHP_EOL);
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
}
$customersDeleteCount = 0;
$scheduleCount = 0;

//loop thru the list created above and delete the customers
/* if (file_exists($delete_set)) {
	$token = NULL;	
	$row = 1;
	if (($handle = fopen("customers.delete.csv", "r")) !== FALSE) {
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
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Authorization: Basic ' . $auth_token,
					'X-Forte-Auth-Organization-id: ' . $organization_id,
					'Accept:application/json',
					'Content-type: application/json'
				));
				$response = curl_exec($ch);
				$info = curl_getinfo($ch);
				curl_close($ch);
				$data = json_decode($response);

				if($info['http_code']==200) {
					$customersDeleteCount++;
				}
			}
		}
		fclose($handle);
	}
	$remaining = $customers_number_results - $customersDeleteCount;
} */
//fclose($sweet);
$leftovers1 = 'CUSTOMER*';
$leftovers2 = 'AlarmBiller*';
$leftovers3 = 'failure*';
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers1));
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers2));
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers3));
//unlink('customers.delete.csv');

//goToPaymethods:
//@include('IMPORT-delete.paymethods.php');
?>