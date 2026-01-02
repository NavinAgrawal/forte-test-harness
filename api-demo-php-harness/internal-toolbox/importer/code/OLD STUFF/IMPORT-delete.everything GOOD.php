<?php
//this script deletes EVERYTHING in the mid

ini_set('max_execution_time', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$endpoint = $_GET['endpoint'];
$auth_token = $_GET['auth_token'];
$organization_id = $_GET['organization_id'];
$base_url = $_GET['base_url'];
$location_id = $_GET['location_id'];

$delete_set = "schedules.delete.csv";
$c = 0;
$rowCount = 0;

//Do a GET call and create a csv of schedule ID's
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
	
	if($info['http_code']==401) {
		$message = "HTTP 401 Invalid Authentication.\\nCheck your REST credentials, and whether you have sandbox or production selected.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
		exit;
	}

	$schedules_number_results = $data["number_results"];
	$rowcount = 0;
	$row = $rowcount;

	if ($schedules_number_results == 0) {
		$schedulesDeleteCount = 0;
		goto goToCustomers;
	}
	
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
		$schedule_id = ($data["results"][$i]["schedule_id"]);
		$sweet = fopen($delete_set, "a+");
		fwrite($sweet, $schedule_id . PHP_EOL);
		$rowcount++;
		$row++;
	}
	
	// if the word "schedule_status" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "schedule_status";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		elseif($resultcount >= 1000){
			$c++;
		}
}
$schedulesDeleteCount = 0;

//loop thru the list created above and delete the schedules
if (file_exists($delete_set)) {
	$token = NULL;	
	$row = 1;
	if (($handle = fopen("schedules.delete.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle)) !== FALSE) {
			$num = count($data);
			$row++;
			for ($c=0; $c < $num; $c++) {
				$token = $data[$c];
				//$endpoint3 = $endpoint2.$token;
				$endpoint2 = $_GET['endpoint2'].$token;
				$ch = curl_init($endpoint2);
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
					$schedulesDeleteCount++;
				}
			}
		}
		fclose($handle);
	}
}

fclose($sweet);
$leftovers1 = 'CUSTOMER*';
$leftovers2 = 'AlarmBiller*';
$leftovers3 = 'failure*';
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers1));
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers2));
array_map("unlink", glob('../../../internal-toolbox/importer/' . $leftovers3));
unlink('schedules.delete.csv');

goToCustomers:
@include('IMPORT-delete.customers.php');
?>